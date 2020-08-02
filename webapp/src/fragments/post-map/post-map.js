/**
 * PostMap component.
 * Renders an leaflet map using the post map data
 * passed via props
 * @listens redrawAndFitMap [via eventBus] - event that will trigger an map redraw and refit bounds - expects {isMaximized: Boolean, guid: String}
 */

import { LMap, LPolyline, LTileLayer, LMarker, LTooltip, LPopup, LControlZoom, LControlAttribution, LControlScale, LControlLayers, LLayerGroup } from 'vue2-leaflet'
import utils from '@/support/utils'
import GeoUtils from '@/support/geo-utils'

import FileExtractorBuilder from '@/support/file-data-extractors/file-extractor-builder'
import * as leaflet from 'leaflet'
import { GestureHandling } from 'leaflet-gesture-handling'
import 'leaflet/dist/leaflet.css'
import 'leaflet-gesture-handling/dist/leaflet-gesture-handling.css'

leaflet.Map.addInitHook('addHandler', 'gestureHandling', GestureHandling)

const tileProviders = [
  {
    name: 'Open Street Maps',
    id: 'osm',
    visible: false,
    attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    token: null
  },
  {
    name: 'Satellite',
    visible: false,
    id: 'satellite',
    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
    token: null
  },
  {
    name: 'Cycling',
    id: 'cycling',
    visible: true,
    url: 'https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png',
    attribution: '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  },
  {
    name: 'Topography',
    id: 'topography',
    visible: false,
    url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
    attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
    token: null
  },
  {
    name: 'Transport Dark',
    visible: false,
    id: 'transport-dark',
    url: 'https://{s}.tile.thunderforest.com/transport-dark/{z}/{x}/{y}.png?apikey=13efc496ac0b486ea05691c820824f5f',
    attribution: 'Maps &copy; <a href="http://thunderforest.com/">Thunderforest</a>, Data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    token: null
  }
]

export default {
  props: {
    post: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      tileProviders: tileProviders,
      mapOptions: { zoomControl: true, attributionControl: true },
      layersPosition: 'topright',
      zoom: 13,
      map: null,
      mapHeight: 300,
      initialMaxZoom: 18,
      routeColor: this.$vuetify.theme.secondary,
      guid: null,
      mapData: null,
      info: null,
      boxGuid: null,
      loaded: false,
      transportationColorMap: [],
      mapRoutes: [],
      showStops: true,
      showStopsControlRef: null
    }
  },
  computed: {
    options () {
      let tooltip = this.routeToolTip
      return {
        onEachFeature: (feature, layer) => {
          layer.bindTooltip(tooltip, { permanent: false, sticky: true })
        }
      }
    },
    markers () {
      if (this.mapData) {
        if (!this.showStops) {
          return []
        }
        return this.mapData.markers
      }
    },
    routes () {
      if (this.post.extra.has_route && Array.isArray(this.mapRoutes)) {
        return this.mapRoutes
      }
      return []
    },
    maxZoom () {
      return this.initialMaxZoom
    },
    title () {
      if (this.post.extra && this.post.extra.map_title) {
        return this.post.extra.map_title
      } else {
        if (Object.keys(this.post.places).length === 1) {
          let keys = Object.keys(this.post.places)
          let lastKey = keys[0]
          return this.post.places[lastKey].title
        }
        return this.$t('postMap.title')
      }
    },
    height () {
      return this.mapHeight
    },
    mapCenter () {
      let center = leaflet.latLng(0, 0)
      if (this.markers.length === 1) {
        let marker = this.markers[0]
        center = leaflet.latLng(marker.position.lat, marker.position.lng)
      }
      return center
    }
  },
  watch: {
    '$route': function () {
      this.loadMapData()
    }
  },

  methods: {
    buildTransportationColorMap () {
      this.transportationColorMap = [
        {
          id: 'bicycle',
          color: '#008000', // green,
          title: this.$t('postMap.transportationMeans.bicycle')
        },
        {
          id: 'foot',
          color: '#808000', // olive
          title: this.$t('postMap.transportationMeans.foot')
        },
        {
          id: 'train',
          color: '#800080', // purple
          title: this.$t('postMap.transportationMeans.train')
        },
        {
          id: 'ferry',
          color: 'red', // green,
          title: this.$t('postMap.transportationMeans.ferry')
        },
        {
          id: 'bus',
          color: '#008000', // maroom,
          title: this.$t('postMap.transportationMeans.bus')
        },
        {
          id: 'sailboat',
          color: '#0000A0', // dark blue,
          title: this.$t('postMap.transportationMeans.sailboat')
        },
        {
          id: 'car',
          color: '#FFA500', // orange,
          title: this.$t('postMap.transportationMeans.car')
        },
        {
          id: 'airplane',
          color: '#000000', // black,
          title: this.$t('postMap.transportationMeans.airplane')
        }
      ]
    },

    buildRoutes () {
      this.mapRoutes = []
      return new Promise((resolve, reject) => {
        if (this.post.extra.has_route && this.post.routes) {
          for (let key in this.post.routes) {
            let route = this.post.routes[key]

            if (route.route_content_type === 'coordinates_array') {
              let polylineFromArr = JSON.parse(route.route_content)
              if (Array.isArray(polylineFromArr)) {
                route.polyline = polylineFromArr
              } else {
                route.polyline = route
              }
              if (route.coordinates_order === 'lng-lat') {
                route.polyline = GeoUtils.switchLatLonIndex(route.polyline)
              }
              this.mapRoutes.push(route)
              resolve()
            } else {
              let routeContentType = route.route_content_type === 'ors_json' ? 'json' : route.route_content_type
              let data = {
                mapRawData: route.route_content,
                options: {}
              }
              let fileExtractorBuilder = new FileExtractorBuilder(routeContentType, data)
              fileExtractorBuilder.buildMapData().then((mapViewData) => {
                for (let routeKey in mapViewData.routes) {
                  route.polyline = mapViewData.routes[routeKey].geometry.coordinates
                  if (route.coordinates_order === 'lng-lat') {
                    route.polyline = GeoUtils.switchLatLonIndex(route.polyline)
                  }
                  this.mapRoutes.push(route)
                }
                resolve()
              }).catch(err => {
                console.log(err)
                resolve()
              })
            }
          }
        } else {
          resolve()
        }
      })
    },
    getColorByTransportation (transportation) {
      let transportationFound = this.lodash.find(this.transportationColorMap, (t) => {
        return t.id === transportation
      })
      if (transportationFound) {
        return transportationFound.color
      } else {
        return 'grey'
      }
    },
    addRouteLegends () {
      if (this.routes.length > 0) {
        this.buildTransportationColorMap()
        let legend = leaflet.control({
          position: 'bottomright'
        })
        let context = this
        legend.onAdd = function () {
          var div = leaflet.DomUtil.create('div', 'map-legend')
          let addedMeans = []
          for (let key in context.routes) {
            let means = context.lodash.find(context.transportationColorMap, (m) => {
              return m.id === context.routes[key].means_of_transportation
            })
            if (means && !addedMeans.includes(means.id)) {
              div.innerHTML += `<div class='item-container'><i style="background:${means.color}"></i>${means.title}</div><br>`
              addedMeans.push(means.id)
            }
          }
          return div
        }
        legend.addTo(this.map)
      }
    },
    toggleShowStops () {
      this.showStops = !this.showStops
      this.map.removeControl(this.showStopsControlRef)
      this.addShowStopsControl()
      setTimeout(() => {
        this.fitFeaturesBounds()
      }, 100)
    },
    addShowStopsControl () {
      if (this.routes.length > 0) {
        let control = leaflet.control({ position: 'bottomleft' })
        let context = this
        control.onAdd = function () {
          var stopsFragment = leaflet.DomUtil.create('div', 'map-show-stops')

          let spanEl = document.createElement('span')
          spanEl.innerText = context.showStops ? 'X' : ''
          spanEl.title = context.$t('postMap.toggleShowStops')

          let divEl = document.createElement('div')
          divEl.innerText = context.$t('postMap.showStops')
          divEl.className = 'show-stop-container'
          divEl.title = context.$t('postMap.toggleShowStops')
          divEl.onclick = () => { context.toggleShowStops() }
          divEl.appendChild(spanEl)

          stopsFragment.appendChild(divEl)
          return stopsFragment
        }
        this.showStopsControlRef = control.addTo(this.map)
      }
    },
    boxCreated (guid) {
      this.boxGuid = guid
    },
    loadMapData () {
      let context = this
      this.buildRoutes().then(() => {
        context.dataBounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
        context.mapData = { markers: GeoUtils.buildMarkers(context.getMarkersData(), context.post.extra.has_route, {mapIconUrl: context.$store.getters.options.map_icon_url}) }
        let polylineData = []
        for (let key in context.routes) {
          if (context.routes[key].polyline) {
            polylineData = polylineData.concat(context.routes[key].polyline)
          }
        }
        context.dataBounds = GeoUtils.getBounds(context.dataBounds, context.mapData.markers, polylineData)

        context.setActiveTilesProvider()
        context.fitFeaturesBounds()
        context.redrawMap()
        context.loaded = true
      }).catch(err => {
        console.log(err)
      })
    },

    /**
     * Set the active tiles provider based on the
     * back-end specified tiles provider
     */
    setActiveTilesProvider () {
      if (this.post.extra.tiles_provider_id) {
        // Check if the tiles provider defined is supported by the front-end
        let tileProviderSupported = this.lodash.find(this.tileProviders, (tp) => {
          return tp.id === this.post.extra.tiles_provider_id
        })
        // If the tile provider specified is supported by
        // the the front end then set is as the only one visible
        if (tileProviderSupported) {
          for (let key in this.tileProviders) {
            this.tileProviders[key].visible = false
            let provider = this.tileProviders[key]
            if (provider.id === this.post.extra.tiles_provider_id) {
              this.tileProviders[key].visible = true
            }
          }
        }
      }
    },

     /**
     * Get the markers data based in the response data
     * @returns {Array} markers
     */
    getMarkersData () {
      let markersData = []
      if (this.post) {
        for (let placeKey in this.post.places) {
          let place = this.post.places[placeKey]
          if (place.markers && place.markers.length > 0) {
            let location = place.markers[0]
            markersData.push([location.lng, location.lat, place.title, place])
          } else if (place.lat && place.lng) {
            markersData.push([place.lng, place.lat, place.title, place])
          }
        }
      }
      return markersData
    },
    redrawMap () {
      return new Promise((resolve, reject) => {
        // This is a hack to force leaflet redraw/resize correctly the maps
        // in the case when there are two map viewers and the container of one of them
        // is resized.
        // The candidates map.invalidateSize() and map.eachLayer(function(layer){layer.redraw();});
        // have not worked at all on this case
        // @see https://github.com/Leaflet/Leaflet/issues/694
        setTimeout(() => {
          window.dispatchEvent(new Event('resize'))
          resolve()
        }, 10)
      })
    },
    /**
     * Fit on the map elements in the current map view/zool
     */
    fitFeaturesBounds () {
      let context = this
      return new Promise((resolve, reject) => {
        // If te map object is already defined
        // then we can directly access it
        if (context.map) {
          if (context.markers.length === 1 && context.routes.length === 0) {
            context.zoom = context.post.extra.zoom ? Number(context.post.extra.zoom) : context.zoom
          } else {
            context.map.fitBounds(context.dataBounds, {padding: [20, 20]})
          }
        } else {
          // If not, it wil be available only in the next tick
          this.$nextTick(() => {
            setTimeout(() => {
              if (context.$refs.map) {
                context.map = context.$refs.map.mapObject // work as expected when wrapped in a $nextTick
                if (context.markers.length === 1 && context.routes.length === 0) {
                  context.zoom = context.post.extra.zoom ? Number(context.post.extra.zoom) : context.zoom
                } else {
                  context.map.fitBounds(context.dataBounds, {padding: [20, 20], maxZoom: 18})
                }
              }
              resolve()
            }, 200)
          })
        }
      })
    },
    /**
     * emit marker details clicked event
     * @param {*} marker
     */
    markerInfoClick (marker) {
      this.$emit('placeClicked', marker.data)
    },
    adjustMap (data) {
      if (data.guid === this.boxGuid) {
        window.dispatchEvent(new Event('resize'))
        // if the map is maximized, then the height
        // will be the window height less an offset
        if (data.maximized) {
          this.mapHeight = window.innerHeight - 100
        } else { // if not, the height is fixed
          this.mapHeight = 300
        }
        this.$forceUpdate()
        // After map container box is resized
        // we need to wait a little bit
        // to redraw the map and then
        // wait a little bit more to fit the bounds
        setTimeout(() => {
          // Redraw the map and then wait
          this.redrawMap().then(() => {
            setTimeout(() => {
              // After redrawing and waiting
              // fit the bounds
              this.fitFeaturesBounds()
            }, 500)
          })
        }, 500)
      }
    }
  },
  mounted () {
    // Define a unique identifier to the map component instance
    this.guid = utils.guid()

    this.buildTransportationColorMap()

    // When the box is created, it emit
    // an event to its parent telling the parent its guid
    this.$emit('onCreate', this.guid)

    // When the box that contains the map is resized
    // we need to resize the map height, redraw the map
    // and then fit the bounds
    let context = this
    this.eventBus.$on('redrawAndFitMap', (data) => {
      if (data.guid && data.guid === context.guid) {
        this.adjustMap(data.isMaximized)
      }
    })
    this.$nextTick(() => {
      setTimeout(() => {
        if (context.$refs.map && context.routes.length > 0) {
          // work as expected when wrapped in a $nextTick
          context.map = context.$refs.map.mapObject
          context.addRouteLegends()
          context.addShowStopsControl()
        }
      }, 200)
    })
    // once the map component is mounted, load the map data
    this.loadMapData()
  },
  components: {
    LMap,
    LTileLayer,
    LPolyline,
    LMarker,
    LLayerGroup,
    LTooltip,
    LPopup,
    LControlZoom,
    LControlAttribution,
    LControlScale,
    LControlLayers
  }
}

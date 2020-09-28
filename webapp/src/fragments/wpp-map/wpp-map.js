/**
 * Wpp Map component.
 * Renders an leaflet map using the post map data
 * passed via props
 * @listens redrawAndFitMap [via eventBus] - event that will trigger an map redraw and refit bounds - expects {isMaximized: Boolean, guid: String}
 */

import { LMap, LPolyline, LTileLayer, LMarker, LTooltip, LPopup, LControlZoom, LControlAttribution, LControlScale, LControlLayers, LLayerGroup } from 'vue2-leaflet'
import utils from '@/support/utils'
import GeoUtils from '@/support/geo-utils'
import constants from '@/resources/constants'

import FileExtractorBuilder from '@/support/file-data-extractors/file-extractor-builder'
import PostService from '@/shared-services/post-service'
import * as leaflet from 'leaflet'
import { GestureHandling } from 'leaflet-gesture-handling'
import 'leaflet/dist/leaflet.css'
import 'leaflet-gesture-handling/dist/leaflet-gesture-handling.css'

leaflet.Map.addInitHook('addHandler', 'gestureHandling', GestureHandling)

export default {
  props: {
    mapId: {
      type: Number,
      required: false
    },
    mapData: {
      type: Object,
      required: false
    },
    heightUnit: {
      type: String,
      default: 'px'
    },
    height: {
      type: Number,
      default: 300
    }
  },
  data () {
    return {
      tileProviders: constants.tileProviders,
      mapOptions: { zoomControl: true, attributionControl: true },
      layersPosition: 'topright',
      zoom: 13,
      map: null,
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
      if (this.localMapData) {
        if (!this.showStops) {
          return []
        }
        return this.localMapData.markers
      }
    },
    routes () {
      if (this.mapRoutes && Array.isArray(this.mapRoutes)) {
        return this.mapRoutes
      }
      return []
    },
    maxZoom () {
      return this.initialMaxZoom
    },
    title () {
      return this.localMapData.title.rendered || this.localMapData.title || this.$t('map.title')
    },
    mapHeight () {
      let height = `${this.height}${this.heightUnit}`
      return height
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
          title: this.$t('wppMap.transportationMeans.bicycle')
        },
        {
          id: 'foot',
          color: '#808000', // olive
          title: this.$t('wppMap.transportationMeans.foot')
        },
        {
          id: 'train',
          color: '#800080', // purple
          title: this.$t('wppMap.transportationMeans.train')
        },
        {
          id: 'ferry',
          color: 'red', // green,
          title: this.$t('wppMap.transportationMeans.ferry')
        },
        {
          id: 'bus',
          color: '#008000', // maroom,
          title: this.$t('wppMap.transportationMeans.bus')
        },
        {
          id: 'sailboat',
          color: '#0000A0', // dark blue,
          title: this.$t('wppMap.transportationMeans.sailboat')
        },
        {
          id: 'car',
          color: '#FFA500', // orange,
          title: this.$t('wppMap.transportationMeans.car')
        },
        {
          id: 'airplane',
          color: '#000000', // black,
          title: this.$t('wppMap.transportationMeans.airplane')
        }
      ]
    },

    setRoutes () {
      this.mapRoutes = []
      let context = this
      return new Promise((resolve, reject) => {
        if (context.localMapData.routes && context.localMapData.routes.length > 0) {
          context.buildRoutes(context.localMapData.routes).then(() => {
            resolve()
          })
        } else {
          resolve()
        }
      })
    },
    buildRoutes (rawRoutes) {
      let context = this
      return new Promise((resolve, reject) => {
        let promises = []
        for (let key in rawRoutes) {
          let rawRoute = rawRoutes[key]

          if (rawRoute.route_content_type === 'coordinates_array') {
            let route = context.buildRouteFromArray(rawRoute)
            context.mapRoutes.push(route)
          } else {
            promises.push(context.buildRoutesFromObject(rawRoute))
          }
        }
        if (promises.length > 0) {
          Promise.all(promises).then((routes) => {
            context.mapRoutes = context.mapRoutes.concat(routes)
            resolve()
          })
        } else {
          resolve()
        }
      })
    },
    buildRoutesFromObject (route) {
      return new Promise((resolve, reject) => {
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
          }
          delete route.route_content
          resolve(route)
        }).catch(err => {
          console.log(err)
          resolve()
        })
      })
    },
    buildRouteFromArray (rawRoute) {
      if (rawRoute.route_content_type === 'coordinates_array') {
        let polylineFromArr = JSON.parse(rawRoute.route_content)
        if (Array.isArray(polylineFromArr)) {
          rawRoute.polyline = polylineFromArr
        } else {
          rawRoute.polyline = rawRoute
        }
        if (rawRoute.coordinates_order === 'lng-lat') {
          rawRoute.polyline = GeoUtils.switchLatLonIndex(rawRoute.polyline)
        }
        return rawRoute
      }
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
          spanEl.title = context.$t('wppMap.toggleShowStops')

          let divEl = document.createElement('div')
          divEl.innerText = context.$t('wppMap.showStops')
          divEl.className = 'show-stop-container'
          divEl.title = context.$t('wppMap.toggleShowStops')
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
      return new Promise((resolve, reject) => {
        context.setRoutes().then(() => {
          context.dataBounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
          let iconUrl = context.$store.getters.options.map_icon_url
          let hasRoute = context.mapRoutes.length > 0
          context.localMapData.markers = GeoUtils.buildMarkers(context.getMarkersData(), hasRoute, {mapIconUrl: iconUrl})
          let polylineData = []

          // build the polyline
          for (let key in context.routes) {
            if (context.routes[key].polyline) {
              polylineData = polylineData.concat(context.routes[key].polyline)
            }
          }
          context.dataBounds = GeoUtils.getBounds(context.dataBounds, context.localMapData.markers, polylineData)

          context.setActiveTilesProvider()
          context.fitFeaturesBounds()
          context.redrawMap()
          context.loaded = true
          resolve()
        }).catch(err => {
          console.log(err)
        })
      })
    },

    /**
     * Set the active tiles provider based on the
     * back-end specified tiles provider
     */
    setActiveTilesProvider () {
      if (this.localMapData.extra.tiles_provider_id) {
        // Check if the tiles provider defined is supported by the front-end
        let tileProviderSupported = this.lodash.find(this.tileProviders, (tp) => {
          return tp.id === this.localMapData.extra.tiles_provider_id
        })
        // If the tile provider specified is supported by
        // the the front end then set is as the only one visible
        if (tileProviderSupported) {
          for (let key in this.tileProviders) {
            this.tileProviders[key].visible = false
            let provider = this.tileProviders[key]
            if (provider.id === this.localMapData.extra.tiles_provider_id) {
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
      for (let placeKey in this.localMapData.places) {
        let place = this.localMapData.places[placeKey]
        if (place.markers && place.markers.length > 0) {
          let location = place.markers[0]
          markersData.push([location.lng, location.lat, place.title, place])
        } else if (place.lat && place.lng) {
          markersData.push([place.lng, place.lat, place.title, place])
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
          context.fit()
        } else {
          // If not, it wil be available only in the next tick
          this.$nextTick(() => {
            setTimeout(() => {
              if (context.$refs.map) {
                context.map = context.$refs.map.mapObject // work as expected when wrapped in a $nextTick
                context.fit()
              }
              resolve()
            }, 200)
          })
        }
      })
    },
    fit () {
      if (this.markers.length === 1 && this.routes.length === 0) {
        this.zoom = this.post.extra.zoom ? Number(this.post.extra.zoom) : this.zoom
      } else {
        this.map.fitBounds(this.dataBounds, {padding: [20, 20], maxZoom: 18})
      }
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
      // Use the map data if passed
      if (context.mapData) {
        context.localMapData = context.mapData
        context.loadMapData().then(() => {
          if (context.$refs.map) {
            // work as expected when wrapped in a $nextTick
            context.map = context.$refs.map.mapObject
          }
        })
      } else { // if not, load it
        // once the map component is mounted,
        // load the map data based on its id
        let endpoint = `maps/${this.mapId}`
        PostService.get(endpoint).then((post) => {
          context.localMapData = post
          this.loadMapData().then(() => {
            if (context.$refs.map) {
              // work as expected when wrapped in a $nextTick
              context.map = context.$refs.map.mapObject
              if (context.routes.length > 0) {
                context.addRouteLegends()
                context.addShowStopsControl()
              }
            }
          })
        })
      }
    })
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

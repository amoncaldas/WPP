/**
 * PostMap component.
 * Renders an leaflet map using the post map data
 * passed via props
 * @listens redrawAndFitMap [via eventBus] - event that will trigger an map redraw and refit bounds - expects {isMaximized: Boolean, guid: String}
 */

import { LMap, LPolyline, LTileLayer, LMarker, LTooltip, LPopup, LControlZoom, LControlAttribution, LControlScale, LControlLayers, LLayerGroup } from 'vue2-leaflet'
import utils from '@/support/utils'
import GeoUtils from '@/support/geo-utils'

import * as L from 'leaflet'
import { GestureHandling } from 'leaflet-gesture-handling'
import 'leaflet/dist/leaflet.css'
import 'leaflet-gesture-handling/dist/leaflet-gesture-handling.css'

L.Map.addInitHook('addHandler', 'gestureHandling', GestureHandling)

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
      transportationColorMap: []
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
        return this.mapData.markers
      }
    },
    routes () {
      let routes = []
      if (this.post.extra.has_route && this.post.routes) {
        for (let key in this.post.routes) {
          let route = this.post.routes[key]
          let polylineFromArr = JSON.parse(route.polyline)
          if (Array.isArray(polylineFromArr)) {
            route.polyline = polylineFromArr
          } else {
            route.polyline = route
          }
          routes.push(route)
        }
      }
      return routes
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
    addLegends () {
      if (this.routes.length > 0) {
        this.buildTransportationColorMap()
        let legend = L.control({
          position: 'bottomright'
        })
        let context = this
        legend.onAdd = function () {
          var div = L.DomUtil.create('div', 'map-legend')
          for (let key in context.routes) {
            let means = context.lodash.find(context.transportationColorMap, (m) => {
              return m.id === context.routes[key].means_of_transportation
            })
            if (means) {
              div.innerHTML += `<div class='item-container'><i style="background:${means.color}"></i>${means.title}</div><br>`
            }
          }
          return div
        }
        legend.addTo(this.map)
      }
    },
    boxCreated (guid) {
      this.boxGuid = guid
    },
    loadMapData () {
      this.dataBounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
      this.mapData = { markers: GeoUtils.buildMarkers(this.getMarkersData(), this.post.extra.has_route, {mapIconUrl: this.$store.getters.options.map_icon_url}) }
      let polylineData = []
      for (let key in this.routes) {
        if (this.routes[key].polyline) {
          polylineData = polylineData.concat(this.routes[key].polyline)
        }
      }
      this.dataBounds = GeoUtils.getBounds(this.dataBounds, this.mapData.markers, polylineData)

      this.setActiveTilesProvider()
      this.fitFeaturesBounds()
      this.redrawMap()
      this.loaded = true
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
          context.map.fitBounds(context.dataBounds, {padding: [20, 20]})
          if (context.markers.length === 1 && context.routes.length === 0) {
            context.zoom = context.post.extra.zoom ? Number(context.post.extra.zoom) : context.zoom
          }
        } else {
          // If not, it wil be available only in the next tick
          this.$nextTick(() => {
            if (context.$refs.map) {
              context.map = context.$refs.map.mapObject // work as expected when wrapped in a $nextTick
              context.map.fitBounds(context.dataBounds, {padding: [20, 20], maxZoom: 18})
              if (context.markers.length === 1 && context.routes.length === 0) {
                context.zoom = context.post.extra.zoom ? Number(context.post.extra.zoom) : context.zoom
              }
            }
            resolve()
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
      if (context.$refs.map && context.routes.length > 0) {
        // work as expected when wrapped in a $nextTick
        context.map = context.$refs.map.mapObject
        context.addLegends()
      }
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

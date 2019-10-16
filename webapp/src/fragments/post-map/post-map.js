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
    visible: true,
    attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    token: null
  },
  {
    name: 'Satellite',
    visible: false,
    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
    token: null
  },
  {
    name: 'Topography',
    visible: false,
    url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
    attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
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
      loaded: false
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
    polyline () {
      if (this.post && this.post.extra) {
        if (this.post.extra.encodedpolyline) {
          let polylineFromEncoded = GeoUtils.decodePolyline(this.post.extra.encodedpolyline)
          return Array.isArray(polylineFromEncoded)? polylineFromEncoded : []
        } else if (this.post.extra.polyline) {
          let polylineFromArr = JSON.parse(this.post.extra.polyline)
          if (Array.isArray(polylineFromArr)) {
            return GeoUtils.switchLatLonIndex(polylineFromArr)
          }
          return []
        }
      }
    },
    maxZoom () {
      return this.initialMaxZoom
    },
    title () {
      if ( Object.keys(this.post.places).length === 1) {
        let keys = Object.keys(this.post.places)
        let lastKey = keys[0]
        return this.post.places[lastKey].title
      }
      return this.$t('postMap.title')
    }
  },

  methods: {
    boxCreated (guid) {
      this.boxGuid = guid
    },
    loadMapData () {
      this.dataBounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
      this.mapData = { markers: GeoUtils.buildMarkers(this.getMarkersData(), this.post.extra.has_route, {mapIconUrl: this.$store.getters.options.map_icon_url}) }
      this.mapData.polyline = this.post.route
      this.dataBounds = GeoUtils.getBBoxAndMarkersBounds(this.dataBounds, this.mapData.markers)
      this.loaded = true
      this.fitFeaturesBounds()
      this.redrawMap()
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
        } else {
          // If not, it wil be available only in the next tick
          this.$nextTick(() => {
            if (context.$refs.map) {
              context.map = context.$refs.map.mapObject // work as expected when wrapped in a $nextTick
              context.map.fitBounds(context.dataBounds, {padding: [20, 20], maxZoom: 18})
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
      this.$emit('placeClicked', marker.json)
    },
    adjustMap (data) {
      if (data.guid === this.boxGuid) {
        window.dispatchEvent(new Event('resize'))
        // if the map is maximized, then the height
        // will be the window height less an offset
        if (data.maximized) {
          this.mapHeight = window.innerHeight - 300
        } else { // if not, the height is fixed
          this.mapHeight = 300
        }
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
    },
  },
  mounted () {
    // Define a unique identifier to the map component instance
    this.guid = utils.guid()

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

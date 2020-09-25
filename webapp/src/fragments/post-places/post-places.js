/**
 * PostPlaces component.
 * Renders an leaflet map using the post map data
 * passed via props
 * @listens redrawAndFitMap [via eventBus] - event that will trigger an map redraw and refit bounds - expects {isMaximized: Boolean, guid: String}
 */

import { LMap, LPolyline, LTileLayer, LMarker, LTooltip, LPopup, LControlZoom, LControlAttribution, LControlScale, LControlLayers, LLayerGroup } from 'vue2-leaflet'
import utils from '@/support/utils'
import GeoUtils from '@/support/geo-utils'
import constants from '@/resources/constants'

import * as leaflet from 'leaflet'
import { GestureHandling } from 'leaflet-gesture-handling'
import 'leaflet/dist/leaflet.css'
import 'leaflet-gesture-handling/dist/leaflet-gesture-handling.css'

leaflet.Map.addInitHook('addHandler', 'gestureHandling', GestureHandling)

export default {
  props: {
    post: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      tileProviders: constants.tileProviders,
      mapOptions: { zoomControl: true, attributionControl: true },
      layersPosition: 'topright',
      zoom: 13,
      map: null,
      mapHeight: 300,
      initialMaxZoom: 18,
      guid: null,
      mapData: null,
      info: null,
      boxGuid: null,
      loaded: false
    }
  },
  computed: {
    options () {
      return { }
    },
    markers () {
      return this.mapData.markers
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
        return this.$t('postPlaces.title')
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
    boxCreated (guid) {
      this.boxGuid = guid
    },
    loadMapData () {
      let context = this
      context.dataBounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
      let iconUrl = context.$store.getters.options.map_icon_url
      context.mapData = { markers: GeoUtils.buildMarkers(context.getMarkersData(), false, {mapIconUrl: iconUrl}) }
      context.dataBounds = GeoUtils.getBounds(context.dataBounds, context.mapData.markers, [])

      context.setActiveTilesProvider()
      context.fitFeaturesBounds()
      context.redrawMap()
      context.loaded = true
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
      for (let placeKey in this.post.places) {
        let place = this.post.places[placeKey]
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
      if (this.markers.length === 1) {
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
        if (context.$refs.map) {
          // work as expected when wrapped in a $nextTick
          context.map = context.$refs.map.mapObject
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

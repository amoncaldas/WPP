/**
 * OrsMap component.
 * Renders an leaflet map using the ors api response
 * passed via props
 * @listens redrawAndFitMap [via eventBus] - event that will trigger an map redraw and refit bounds - expects {isMaximized: Boolean, guid: String}
 */

import { LMap, LTileLayer, LMarker, LTooltip, LPopup, LControlZoom, LControlAttribution, LControlScale, LControlLayers, LLayerGroup } from 'vue2-leaflet'
import utils from '@/support/utils'
import GeoUtils from '@/support/geo-utils'

import theme from '@/common/theme'

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
      routeColor: theme.primary,
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
    maxZoom () {
      return this.initialMaxZoom
    }
  },

  methods: {
    boxCreated (guid) {
      this.boxGuid = guid
    },
    loadMapData () {
      this.dataBounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
      this.mapData = { markers: GeoUtils.buildMarkers(this.getMarkersData(), false, {mapIconUrl: this.$store.getters.options.map_icon_url}) }
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
      if (this.$store.getters.sections) {
        this.$store.getters.sections.forEach(section => {
          for (let placeKey in section.places) {
            let place = section.places[placeKey]
            if (place.markers.length > 0) {
              let location = place.markers[0]
              markersData.push([location.lng, location.lat, section.title.rendered, section])
            }
          }
        })
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
              context.map.scrollWheelZoom.disable()
            }
            resolve()
          })
        }
      })
    },
    markerInfoClick (section) {
      this.$router.push(section.json.path)
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
    // once the map component is mounted, load the map data
    this.loadMapData()
  },
  components: {
    LMap,
    LTileLayer,
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

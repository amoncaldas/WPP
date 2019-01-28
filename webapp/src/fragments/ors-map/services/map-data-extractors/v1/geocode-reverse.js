import GeoUtils from '@/support/geo-utils'
import VueInstance from '@/main'

/**
 * GeocodeReverseBuilder Map data Builder class
 * @param {*} data {responseData: {}, translations: {}}
 */
class GeocodeReverseBuilder {
  constructor (data) {
    this.responseData = data.responseData
    this.markers = null
  }

  /**
   * Build the map data for geocode reverse response
   * @returns {Promise} that returns in the resolve mapData object
   */
  buildMapData = () => {
    let mapData = {}
    return new Promise((resolve) => {
      let bounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
      mapData.markers = this.buildMarkers()
      mapData.bbox = GeoUtils.getBBoxAndMarkersBounds(bounds, mapData.markers)
      mapData.maxZoom = 20
      resolve(mapData)
    })
  }

  /**
   * Build the markers based in an array of coordinates
   * @returns {Array} of markers
   */
  buildMarkers = () => {
    let markersData = this.getMarkersData()
    let markers = []
    VueInstance.lodash.each(markersData, (wayPoint, key) => {
      // define the color: all are red, except for the last one (the reference point) that is blue
      let coloredMarkerName = 'red'
      if (key === markersData.length - 1) {
        coloredMarkerName = 'blue'
      }
      // Build the marker
      let markerIcon = GeoUtils.buildMarkerIcon(coloredMarkerName)
      let marker = { position: { lng: wayPoint[0], lat: wayPoint[1] }, icon: markerIcon }
      // if the way point array has the third parameter, it is its label
      if (wayPoint[2]) { marker.label = wayPoint[2] }
      // if the way point array has the fourth parameter, it is its way point json data
      if (wayPoint[3]) { marker.json = wayPoint[3] }
      markers.push(marker)
    })
    return markers
  }

   /**
   * Get the markers data based in the response data
   * @returns {Array} markersData
   */
  getMarkersData = () => {
    let markersData = []
    if (this.responseData.features) {
      this.responseData.features.forEach(feature => {
        markersData.push([feature.geometry.coordinates[0], feature.geometry.coordinates[1], feature.properties.label, feature])
      })
      let queryPoint = this.responseData.geocoding.query
      let label = `${queryPoint['point.lon']}, ${queryPoint['point.lat']}`
      markersData.push([queryPoint['point.lon'], queryPoint['point.lat'], label])
    }
    return markersData
  }
}

// export the directions json builder class
export default GeocodeReverseBuilder

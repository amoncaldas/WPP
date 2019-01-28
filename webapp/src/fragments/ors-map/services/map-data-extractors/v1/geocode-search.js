import GeoUtils from '@/support/geo-utils'
/**
 * GeocodeSearchBuilder Map data Builder class
 * @param {*} data {responseData: {}, translations: {}}
 */
class GeocodeSearchBuilder {
  constructor (data) {
    this.responseData = data.responseData
    this.markers = null
  }

  /**
   * Build the map data for geocode search response
   * @returns {Promise} that returns in the resolve mapData object
   */
  buildMapData = () => {
    let mapData = {}
    return new Promise((resolve) => {
      let bounds = [{lon: 0, lat: 0}, {lon: 0, lat: 0}]
      mapData.markers = GeoUtils.buildMarkers(this.getMarkersData(), false)
      mapData.bbox = GeoUtils.getBBoxAndMarkersBounds(bounds, mapData.markers)
      resolve(mapData)
    })
  }

   /**
   * Get the markers data based in the response data
   * @returns {Array} markers
   */
  getMarkersData = () => {
    let markersData = []
    if (this.responseData && this.responseData.features) {
      this.responseData.features.forEach(feature => {
        markersData.push([feature.geometry.coordinates[0], feature.geometry.coordinates[1], feature.properties.label, feature])
      })
    }
    return markersData
  }
}

// export the directions json builder class
export default GeocodeSearchBuilder

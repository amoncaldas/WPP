import main from '@/main'
import GeoUtils from '@/support/geo-utils'

/**
 * DirectionsJSONBuilder Map data Builder class
 * @param {*} data {responseData: {}, translations: {}}
 */
class DirectionsJSONBuilder {
  constructor (data) {
    this.responseData = data.responseData
  }

  /**
   * Build the map data for directions json response
   * @returns {Promise} that returns in the resolve mapData object
   */
  buildMapData = () => {
    let mapData = {}
    let context = this
    return new Promise((resolve) => {
      mapData.markers = this.buildMarkers()
      mapData.polyLineData = this.getPolyLineData()
      mapData.routeSummaryData = this.getRouteSummaryData()
      let VueInstance = main.getInstance()
      if (VueInstance.lodash.get(context, 'responseData.features[0].properties.summary[0]')) {
        mapData.geoJson = context.responseData
        mapData.bbox = GeoUtils.getBBoxAndMarkersBounds(context.responseData.bbox, mapData.markers)
      } else if (VueInstance.lodash.get(context, 'responseData.routes[0].bbox')) {
        mapData.bbox = GeoUtils.getBBoxAndMarkersBounds(context.responseData.routes[0].bbox, mapData.markers)
      }
      resolve(mapData)
    })
  }

  /**
   * Get the markers data based in the response data
   * @returns {Array} markersData
   */
  buildMarkers = () => {
    let markersData = []
    let VueInstance = main.getInstance()
    if (VueInstance.lodash.get(this, 'geoJson.info.query.coordinates')) {
      markersData = this.geoJson.info.query.coordinates
    } else if (VueInstance.lodash.get(this, 'responseData.info.query.coordinates')) {
      markersData = this.responseData.info.query.coordinates
    }
    return GeoUtils.buildMarkers(markersData)
  }

  /**
   * Get the polyline tool tip data
   * @returns {Array} coordinates
   */
  getRouteSummaryData = () => {
    let summary = {}
    let VueInstance = main.getInstance()
    if (VueInstance.lodash.get(this, 'responseData.routeSummary')) {
      summary = this.responseData.routeSummary.summary
    } else if (VueInstance.lodash.get(this, 'responseData.features[0].properties.summary[0]')) {
      summary = this.responseData.features[0].properties.summary[0]
    } else if (VueInstance.lodash.get(this, 'responseData.routes[0].summary')) {
      summary = this.responseData.routes[0].summary
    }
    return {
      unit: this.responseData.info.query.units,
      distance: summary.distance,
      duration: summary.duration
    }
  }

  /**
   * Get the polyline data if the response contains polyline data
   * @returns {Array} coordinates
   */
  getPolyLineData = () => {
    if (this.responseData.info.query) {
      switch (this.responseData.info.query.geometry_format) {
        case 'polyline':
          return GeoUtils.switchLatLonIndex(this.responseData.routes[0].geometry)
        case 'encodedpolyline':
          return GeoUtils.decodePolyline(this.responseData.routes[0].geometry)
        case 'geojson':
          let coordinates = this.responseData.routes ? this.responseData.routes[0].geometry.coordinates : this.responseData.features[0].geometry.coordinates
          return GeoUtils.switchLatLonIndex(coordinates)
      }
    }
  }
}

// export the directions json builder class
export default DirectionsJSONBuilder

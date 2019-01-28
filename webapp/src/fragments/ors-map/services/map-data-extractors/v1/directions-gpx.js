import HttpOrsApi from '@/shared-services/http-ors-api'
import VueInstance from '@/main'
import GeoUtils from '@/support/geo-utils'

/**
 * DirectionsGPXBuilder Map data Builder class
 * @param {*} data {responseData: {}, requestData: {}, translations: {}}
 */
class DirectionsGPXBuilder {
  constructor (data) {
    this.responseData = data.responseData
    this.requestData = data.requestData
    this.translations = data.translations
    this.gpxSecondRequestResponseData = null
  }

  /**
   * Build the map data for directions gpx response
   * @returns {Promise} that returns in the resolve mapData object
   */
  buildMapData = () => {
    let mapData = {}
    let context = this
    return new Promise((resolve) => {
      this.redoRequestToGetResponseAsGeoJson().then((response) => {
        context.gpxSecondRequestResponseData = response.data
        mapData.geoJson = response.data
        mapData.polyLineData = this.getPolyLineData()
        mapData.routeSummaryData = this.getRouteSummaryData()
        mapData.markers = this.buildMarkers()
        mapData.bbox = GeoUtils.getBBoxAndMarkersBounds(response.data.bbox, mapData.markers)
        mapData.info = context.translations.viewBasedOnEquivalentGeoJsonResponse
        resolve(mapData)
      })
    })
  }

  /**
   * Get the markers data based in the response data
   * @returns {Array} markersData
   */
  buildMarkers = () => {
    let markersData = []
    if (VueInstance.lodash.get(this, 'gpxSecondRequestResponseData.info.query.coordinates')) {
      markersData = this.gpxSecondRequestResponseData.info.query.coordinates
    }
    return GeoUtils.buildMarkers(markersData)
  }

  /**
   * Get the polyline tool tip data
   * @returns {Object} route summary
   */
  getRouteSummaryData = () => {
    if (this.gpxSecondRequestResponseData) {
      let summary = {}
      if (VueInstance.lodash.get(this, 'gpxSecondRequestResponseData.routeSummary')) {
        summary = this.gpxSecondRequestResponseData.routeSummary.summary
      } else if (VueInstance.lodash.get(this, 'gpxSecondRequestResponseData.features[0].properties.summary[0]')) {
        summary = this.gpxSecondRequestResponseData.features[0].properties.summary[0]
      }
      return {
        unit: this.gpxSecondRequestResponseData.info.query.units,
        distance: summary.distance,
        duration: summary.duration
      }
    }
    return {}
  }

  /**
   * Get the polyline data if the response contains polyline data
   * @returns {Array} coordinates
   */
  getPolyLineData = () => {
    let coordinates = []
    if (this.gpxSecondRequestResponseData) {
      if (this.gpxSecondRequestResponseData.routes) {
        coordinates = this.gpxSecondRequestResponseData.routes[0].geometry.coordinates
      } else {
        coordinates = this.gpxSecondRequestResponseData.features[0].geometry.coordinates
      }
      coordinates = GeoUtils.switchLatLonIndex(coordinates)
    }
    return coordinates
  }

  /**
   * Run a second request to get the response data in json format
   * this is used when the initial api response is in gpx
   * because we need the response in json to be able to display the result in a map
   */
  redoRequestToGetResponseAsGeoJson = () => {
    let url = this.requestData.url.replace('format=gpx', 'format=geojson')
    if (VueInstance.lodash.get(this, 'requestData.body.format')) {
      this.requestData.body.format = 'geojson'
    }
    if (this.requestData.method === 'get') {
      return HttpOrsApi.get(url, this.requestData.headers)
    } else {
      return HttpOrsApi.post(url, this.requestData.body, this.requestData.headers)
    }
  }
}

// export the directions gpx builder class
export default DirectionsGPXBuilder

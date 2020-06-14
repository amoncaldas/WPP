import Main from '@/main'
import Leaflet from 'leaflet'

const geoUtils = {
  /**
   * Reorder the values of an array of coordinates switching the position of lat and long of each coordinate
   * @param {*} coordinatesArr
   * @returns {Array} of reordered coordinates
   */
  switchLatLonIndex: (coordinatesArr) => {
    let switchedCoords = []
    // the lat and long data of the geometry comes in the
    // inverted order from the one that is expected by leaflet
    // so we just iterate over than and create an array
    // with lat and long in the expected order
    coordinatesArr.forEach(function (coordinate) {
      switchedCoords.push([coordinate[1], coordinate[0]])
    })
    return switchedCoords
  },

  /**
   * Get the marker color based on the index, isRoute and lasIndex
   * @param index
   * @param lastIndexKey
   * @param {Boolean} isRoute
   * @returns {Array} of markers
   */
  getMarkerColor: (index, lastIndexKey, isRoute) => {
    let coloredMarkerName
    if (isRoute === false) {
      coloredMarkerName = 'red'
    } else {
      switch (index) {
        case 0: // if first is `teal`
          coloredMarkerName = 'green'
          break
        case lastIndexKey: // if last is `green`
          coloredMarkerName = 'red'
          break
        default: // all intermediate markers are `blue`
          coloredMarkerName = 'blue'
      }
    }
    return coloredMarkerName
  },

  /**
   * Build the markers based in an array of coordinates
   * @param markersData - coordinates of the marker
   * @param {Boolean} isRoute - default true
   * @returns {Array} of markers
   */
  buildMarkers: (markersData, isRoute = true, options = {}) => {
    let markers = []
    let VueInstance = Main.getInstance()
    VueInstance.lodash.each(markersData, (wayPoint, key) => {
      // Define the marker color
      let lastIndexKey = markersData.length - 1
      let coloredMarkerName = geoUtils.getMarkerColor(key, lastIndexKey, isRoute)

      options.isRoute = isRoute
      options.key = key
      options.lastIndexKey = lastIndexKey

      // Build the marker
      let markerIcon = geoUtils.buildMarkerIcon(coloredMarkerName, options)
      let marker = { position: { lng: wayPoint[0], lat: wayPoint[1] }, icon: markerIcon }

      // if the way point array has the third parameter, it is its label
      if (wayPoint[2]) {
        marker.label = wayPoint[2]
      } else {
        marker.label = `${wayPoint[0]}, ${wayPoint[1]}`
      }
      // if the way point array has the fourth parameter, it is its way point json data
      if (wayPoint[3]) { marker.data = wayPoint[3] }

      // Add the markers to the returning array
      markers.push(marker)
    })
    return markers
  },

  /**
   * Build a marker icon based on the color specified
   * Expecting marker PNGs in 180x230 resolution
   * @param {String} color
   * @returns {Object} markerIcon
   */
  buildMarkerIcon: (color, options) => {
    let iconFile = require(`./static/${color}-marker.png`)
    let shadowUrl = require('leaflet/dist/images/marker-shadow.png')
    let iconSize = [28, 36]

    if (options.mapIconUrl && (!options.isRoute || options.key === options.lastIndexKey)) {
      iconFile = options.mapIconUrl
      shadowUrl = null
      iconSize = [36, 36]
    }
    let markerIcon = Leaflet.icon({
      iconUrl: iconFile,
      shadowUrl: shadowUrl,
      iconAnchor: [14, 35],
      shadowAnchor: [12, 41],
      iconSize: iconSize,
      popupAnchor: [0, -32]
    })
    return markerIcon
  },

  /**
   * Decode an encoded polyline
   * @param {*} encodedPolyline
   * @returns {Array} of coordinates
   */
  decodePolyline: (encodedPolyline) => {
    // array that holds the points
    var points = []
    var index = 0
    var len = encodedPolyline.length
    var lat = 0
    var lng = 0
    while (index < len) {
      var b
      var shift = 0
      var result = 0
      do {
        b = encodedPolyline.charAt(index++).charCodeAt(0) - 63 // finds ascii
        // and subtract it by 63
        result |= (b & 0x1f) << shift
        shift += 5
      } while (b >= 0x20)

      var dlat = ((result & 1) !== 0 ? ~(result >> 1) : (result >> 1))
      lat += dlat
      shift = 0
      result = 0
      do {
        b = encodedPolyline.charAt(index++).charCodeAt(0) - 63
        result |= (b & 0x1f) << shift
        shift += 5
      } while (b >= 0x20)
      var dlng = ((result & 1) !== 0 ? ~(result >> 1) : (result >> 1))
      lng += dlng

      points.push([(lat / 1E5), (lng / 1E5)])
    }
    return points
  },

  /**
   * Build a bounding box the also includes the markers
   * @param {Array} bbox
   * @returns {Array} bbox
   */
  getBBoxAndMarkersBounds: (bbox, markers) => {
    let boundsCollection = [{lon: bbox[0], lat: bbox[1]}, {lon: bbox[2], lat: bbox[3]}]

    let minLat = bbox[1]
    let maxLat = bbox[3]
    let minLon = bbox[0]
    let maxLon = bbox[2]

    markers.forEach((marker) => {
      boundsCollection.push({lat: marker.position.lat, lon: marker.position.lng})
    })

    boundsCollection.forEach(latLon => {
      minLat = latLon.lat > minLat ? minLat : latLon.lat
      minLon = latLon.lon > minLon ? minLon : latLon.lon
      maxLat = latLon.lat < maxLat ? maxLat : latLon.lat
      maxLon = latLon.lon < maxLon ? maxLon : latLon.lon
    })
    return [
      {lon: minLon, lat: minLat},
      {lon: maxLon, lat: maxLat}
    ]
  },

  /**
   * Build a bounding box the also includes the plaes and the polyline
   * @param {Array} originalBbox
   * @param {Array} places
   * @returns {Matrix} polyline with arrays of lngLat
   */
  getBounds: (originalBbox, markers = [], polyline = []) => {
    let boundsCollection = [{lon: originalBbox[0], lat: originalBbox[1]}, {lon: originalBbox[2], lat: originalBbox[3]}]

    let minLat = originalBbox[1]
    let maxLat = originalBbox[3]
    let minLon = originalBbox[0]
    let maxLon = originalBbox[2]

    markers.forEach((marker) => {
      boundsCollection.push({lat: marker.position.lat, lon: marker.position.lng})
    })
    if (Array.isArray(polyline)) {
      polyline.forEach((lngLatArr) => {
        boundsCollection.push({lat: lngLatArr[0], lon: lngLatArr[1]})
      })
    }

    boundsCollection.forEach(latLon => {
      minLat = latLon.lat > minLat ? minLat : latLon.lat
      minLon = latLon.lon > minLon ? minLon : latLon.lon
      maxLat = latLon.lat < maxLat ? maxLat : latLon.lat
      maxLon = latLon.lon < maxLon ? maxLon : latLon.lon
    })
    return [
      {lon: minLon, lat: minLat},
      {lon: maxLon, lat: maxLat}
    ]
  },

  /**
   * Get humanized tool tip string
   * @param {*} data {duration: Number, distance: Number, unit: String}
   * @returns {String} formatted tool tip
   */
  getHumanizedTimeAndDistance: (data, translations) => {
    let humanizedDistance = null
    if (data.distance) {
      if (data.distance > 1000 && data.unit === 'm') {
        // when the unit is in meters and very big, we convert it to kilometers
        data.distance = (data.distance / 1000).toFixed(1)
        data.unit = 'km'
      } else {
        // If km or mi, only one decimal
        data.distance = data.distance.toFixed(1)
      }
      humanizedDistance = `${data.distance} ${data.unit}`
    }

    let humanizedDuration = null

    if (data.duration) {
      let durationObj = geoUtils.getDurationInSegments(data.duration, translations)
      humanizedDuration = `${durationObj.days}${durationObj.hours} ${durationObj.minutes} ${durationObj.seconds}`
    }

    return {
      duration: humanizedDuration,
      distance: humanizedDistance
    }
  },

  /**
   * Get the seconds segments (days, hours, minutes, seconds) or empty string for each segment
   * @param {*} data {duration: Number, distance: Number, unit: String}
   * @returns {String} formatted tool tip
   */
  getDurationInSegments: (seconds, translations) => {
    let VueInstance = Main.getInstance()
    let durationObj = VueInstance.$moment.duration(seconds, 'seconds') // the duration value is expected to be always in seconds
    return {
      days: durationObj._data.days > 0 ? durationObj._data.days + ' ' + translations.days : '',
      hours: durationObj._data.hours > 0 ? durationObj._data.hours + ' ' + translations.hours : '',
      minutes: durationObj._data.minutes > 0 ? durationObj._data.minutes + ' ' + translations.minutes : '',
      seconds: durationObj._data.seconds > 0 ? durationObj._data.seconds + ' ' + translations.seconds : ''
    }
  }
}
export default geoUtils

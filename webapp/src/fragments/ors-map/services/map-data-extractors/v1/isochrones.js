import GeoUtils from '@/support/geo-utils'
import main from '@/main'
import htmlColors from 'html-colors'
/**
 * IsochronesBuilder Map data Builder class
 * @param {*} data {responseData: {}, translations: {}}
 */
class IsochronesBuilder {
  constructor (data) {
    this.responseData = data.responseData
    this.translations = data.translations
    this.markers = null
  }

  /**
   * Build the map data for geocode search response
   * @returns {Promise} that returns in the resolve mapData object
   */
  buildMapData = () => {
    let mapData = {}
    return new Promise((resolve) => {
      mapData.markers = GeoUtils.buildMarkers(this.getMarkersData(), false)
      mapData.polygons = this.getPolygons()
      mapData.bbox = this.getBoundingBox(mapData.markers, mapData.polygons)
      resolve(mapData)
    })
  }

  /**
   * Get the markers data based in the response data
   * @returns {Array} markers
   */
  getMarkersData = () => {
    let markersData = []
    if (main.getInstance().lodash.get(this, 'responseData.info.query.locations')) {
      this.responseData.info.query.locations.forEach(location => {
        let locationCoordsStr = `${location[0]}, ${location[1]}`
        markersData.push([location[0], location[1], locationCoordsStr, this.responseData.info.query])
      })
      let polygonCenters = []
      this.responseData.features.forEach(feature => {
        let locationCoordsStr = `${feature.properties.center[0]}, ${feature.properties.center[1]}`
        if (!polygonCenters.includes(locationCoordsStr)) {
          markersData.push([feature.properties.center[0], feature.properties.center[1], locationCoordsStr, feature.properties])
          polygonCenters.push(locationCoordsStr)
        }
      })
    }
    return markersData
  }

  /**
   * Merge the bounds of markers and polygons
   *
   * @param {Array} markers
   * @param {Array} polygons
   * @returns {Array} merged features bounds
   * @memberof IsochronesBuilder
   */
  mergeFeaturesBounds = (markers, polygons) => {
    let boundsCollection = []

    markers.forEach((marker) => {
      boundsCollection.push({lat: marker.position.lat, lon: marker.position.lng})
    })

    polygons.forEach((polygon) => {
      polygon.latlngs.forEach((latlngs) => {
        latlngs.forEach((latlng) => {
          boundsCollection.push({lat: latlng[0], lon: latlng[1]})
        })
      })
    })
    return boundsCollection
  }

    /**
   * Build a bounding box the also includes the markers
   * @param {Array} bbox
   * @returns {Array} bbox
   */
  getBoundingBox = (markers, polygons) => {
    let boundsCollection = this.mergeFeaturesBounds(markers, polygons)

    let minLat = boundsCollection[0].lat
    let maxLat = boundsCollection[0].lat
    let minLon = boundsCollection[0].lon
    let maxLon = boundsCollection[0].lon

    boundsCollection.forEach(latLon => {
      minLat = latLon.lat < minLat ? latLon.lat : minLat
      minLon = latLon.lon < minLon ? latLon.lon : minLon
      maxLat = latLon.lat > maxLat ? latLon.lat : maxLat
      maxLon = latLon.lon > maxLon ? latLon.lon : maxLon
    })
    return [
      {lon: minLon, lat: minLat},
      {lon: maxLon, lat: maxLat}
    ]
  }

  /**
   * Get the markers data based in the response data
   * @returns {Array} markers
   */
  getPolygons = () => {
    let polygons = []
    if (this.responseData.features) {
      let names = htmlColors.names() // Get an array containing all colors names
      let translations = this.translations
      this.responseData.features.forEach((feature, index) => {
        // The Vue2-Leaflet expect the coordinates in the inverse order (lat,long), so we reorder then here
        // The polygon component expects an array containing, at the position 0 an array of coordinates
        // so we just provide it as expected
        let switchedCords = [GeoUtils.switchLatLonIndex(feature.geometry.coordinates[0])]
        let unit = this.responseData.info.query.range_type === 'time' ? translations.seconds : translations.meters
        let featureLabel = `${feature.properties.value} ${unit} ${feature.geometry.type}`
        let polygon = {
          latlngs: switchedCords,
          color: htmlColors.hex(names[index + 6]), // We get a color from the color collection but we skip the first 5 colors because they are not nice
          json: feature,
          label: featureLabel
        }
        polygons.push(polygon)
      })
    }
    return polygons.reverse()
  }
}

// export the directions json builder class
export default IsochronesBuilder

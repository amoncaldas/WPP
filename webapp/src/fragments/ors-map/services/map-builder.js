import DirectionsGPXDataBuilderV1 from './map-data-extractors/v1/directions-gpx'
import DirectionsJSONDataBuilderV1 from './map-data-extractors/v1/directions-json'
import GeocodeSearchBuilderV1 from './map-data-extractors/v1/geocode-search'
import GeocodeReverseBuilderV1 from './map-data-extractors/v1/geocode-reverse'
import IsochronesBuilderV1 from './map-data-extractors/v1/isochrones'

/**
 * Map data Builder class
 * @param {*} data {responseData: {}, requestData: {}, apiVersion: String, translations: {}}
 */
class MapDataBuilder {
  constructor (data) {
    this.dataBuilder = MapDataBuilder.getMapDataBuilder(data)
  }

  /**
   * Build the map data for the target strategy, using the appropriate builder
   * @returns {Promise} that returns in the resolve mapData object
   */
  buildMapData = () => {
    return this.dataBuilder.buildMapData()
  }

  static getMapDataBuilder (data) {
    switch (data.requestData.endpoint) {
      case '/directions':
        if (typeof data.responseData === 'object') {
          return MapDataBuilder.getMapDataExtractor('DirectionsJSONDataBuilder', data)
        } else {
          // This DirectionsGPXDataBuilder needs the request data because it will run a second
          // request (based on the original request) to get the geojson equivalent response
          return MapDataBuilder.getMapDataExtractor('DirectionsGPXDataBuilder', data)
        }
      // `geocode/autocomplete` and `/geocode/search` have the same response structure
      case '/geocode/search':
      case '/geocode/autocomplete':
        return MapDataBuilder.getMapDataExtractor('GeocodeSearchBuilder', data)
      case '/geocode/search/structured':
        return MapDataBuilder.getMapDataExtractor('GeocodeSearchBuilder', data)
      case '/geocode/reverse':
        return MapDataBuilder.getMapDataExtractor('GeocodeReverseBuilder', data)
      case '/isochrones':
        return MapDataBuilder.getMapDataExtractor('IsochronesBuilder', data)
    }
  }

  /**
   * Get the map data extractor based on the current selected api version
   *
   * @param String builderName
   * @param data {responseData: {}, requestData: {}, translations: {}, apiVersion: {String}}
   * @returns {*} Map data extractor instance
   */
  static getMapDataExtractor = (extractorName, data) => {
    // This is a dictionary that translates
    // the API version to an extractor version
    let apiVersionToExtractorVersionDictionary = {
      '4.5': 'V1',
      '4.7': 'V1'
    }

    // In this constant we have to add all the map extractors
    // for all the cases in all versions
    const extractors = {
      DirectionsGPXDataBuilderV1,
      DirectionsJSONDataBuilderV1,
      GeocodeSearchBuilderV1,
      GeocodeReverseBuilderV1,
      IsochronesBuilderV1
      // we can add more versions here in the future
    }

    // Get the right extractor based on the extractor name
    // and on the api version from the extractors list
    let extractorNameWithVersion = extractorName + apiVersionToExtractorVersionDictionary[data.apiVersion]
    let extractor = new extractors[extractorNameWithVersion](data)
    return extractor
  }

  /**
   * Determines if the ors map component has support
   * for given endpoint
   *
   * @static
   * @param {*} data {responseData: {}, requestData: {}, apiVersion: String}
   * @memberof TableDataBuilder
   */
  static hasMapBuilderFor = (data) => {
    let builder = MapDataBuilder.getMapDataBuilder(data)
    return builder !== null && builder !== undefined
  }
}

// export the request builder class
export default MapDataBuilder

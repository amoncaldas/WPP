const constants = {
  dataOrigins: {
    directions: '/directions',
    isochrones: '/isochrones',
    fileImporter: 'fileImporter'
  },
  services: {
    directions: 'directions',
    geocodeSearch: 'geocodeSearch',
    autocomplete: 'autocomplete',
    reverseGeocode: 'reverseGeocode',
    isochrones: 'isochrones'
  },
  filterTypes: {
    wrapper: 'wrapper',
    array: 'array',
    string: 'string',
    steps: 'steps',
    random: 'random',
    text: 'text',
    boolean: 'boolean'
  },
  modes: {
    roundTrip: 'roundtrip',
    directions: 'directions',
    place: 'place',
    search: 'search',
    isochrones: 'isochrones'
  },
  importableModes: {
    roundTrip: 'roundtrip',
    directions: 'directions',
    isochrones: 'isochrones'
  },
  orsKmlDocumentDescription: 'ORS route file'
}

export default constants

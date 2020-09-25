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
  orsKmlDocumentDescription: 'ORS route file',
  tileProviders: [
    {
      name: 'Open Street Maps',
      id: 'osm',
      visible: false,
      attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      token: null
    },
    {
      name: 'Satellite',
      visible: false,
      id: 'satellite',
      url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
      attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
      token: null
    },
    {
      name: 'Cycling',
      id: 'cycling',
      visible: true,
      url: 'https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png',
      attribution: '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    },
    {
      name: 'Topography',
      id: 'topography',
      visible: false,
      url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
      attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
      token: null
    },
    {
      name: 'Transport Dark',
      visible: false,
      id: 'transport-dark',
      url: 'https://{s}.tile.thunderforest.com/transport-dark/{z}/{x}/{y}.png?apikey=13efc496ac0b486ea05691c820824f5f',
      attribution: 'Maps &copy; <a href="http://thunderforest.com/">Thunderforest</a>, Data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      token: null
    }
  ]
}

export default constants

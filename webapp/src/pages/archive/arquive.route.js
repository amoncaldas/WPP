import Archive from './Archive'
import store from '@/store/store'

const routes = {
  get: () => {
    let archiveRutes = []
    store.getters.sectionRoutes.forEach(sectionRoute => {
      archiveRutes = archiveRutes.concat(getSectionRoutes(sectionRoute))
    })
  },
  getEndpoints: () => {
    let endpoints = []
    if(store.getters.options.wpp_post_type_endpoints) {
      endpoints = store.getters.options.wpp_post_type_endpoints
      endpoints = Array.isArray(endpoints) ? endpoints : endpoints.split(',')

      for(key in endpoints) {
        let endpoint = endpoints[key]

        if (store.getters.options.wpp_post_type_translations[endpoint]) {
          let translations = store.getters.options.wpp_post_type_translations[endpoint]
          for(key in translations) {
            let translation = translations[key]
            endpoints.push(translation.url)
          }
        }
      }
    }
    return endpoints
  },
  getSectionRoutes: (section) =>  {
    let endpoints = getEndpoints()

    let routes = []
    endpoints.forEach(endpoint => {
      routes.push(
        {
          path: `/${endpoint}`,
          name: 'Archive',
          component: Archive
        }
      )
      routes.push(
        {
          path: `${section}/${endpoint}`,
          name: 'Archive',
          component: Archive
        }
      )
    });
    return routes
  }
}

const archiveRutes = routes.get()

export default archiveRutes

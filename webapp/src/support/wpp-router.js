import store from '@/store/store'

const wppRouter = {
  getSectionEndpoints: () => {
    return store.getters.sectionRoutes || []
  },
  getPostTypeEndpoints: () => {
    let endpoints = []
    if (store.getters.options.wpp_post_type_endpoints) {
      endpoints = store.getters.options.wpp_post_type_endpoints
      endpoints = Array.isArray(endpoints) ? endpoints : endpoints.split(',')

      for (let key in endpoints) {
        let endpoint = endpoints[key]

        if (store.getters.options.wpp_post_type_translations[endpoint]) {
          let translations = store.getters.options.wpp_post_type_translations[endpoint]
          for (key in translations) {
            let translation = translations[key]
            endpoints.push(translation.url)
          }
        }
      }
    }
    return endpoints
  }
}

export default wppRouter

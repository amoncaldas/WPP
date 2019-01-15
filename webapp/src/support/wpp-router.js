import store from '@/store/store'

const wppRouter = {
  getSectionEndpoints: () => {
    let sectionsRoutes = store.getters.sectionsRoutes
    return sectionsRoutes || []
  },
  getPostTypeEndpoints: () => {
    let endpoints = []
    if (store.getters.options.post_type_endpoints) {
      endpoints = store.getters.options.post_type_endpoints

      for (let key in endpoints) {
        let endpoint = endpoints[key]
        wppRouter.addEndpointTranslations(endpoint, endpoints)
      }
    }
    return endpoints
  },
  addEndpointTranslations: (endpoint, endpoints) => {
    let translations = store.getters.options.post_type_translations
    if (translations) {
      for (let tKey in translations) {
        let translation = translations[tKey]
        let match = false
        for (let key in translation) {
          let locale = translation[key]
          if (locale.url === endpoint) {
            match = true
          }
        }

        if (match) {
          for (let key in translation) {
            let locale = translation[key]
            let includes = endpoints.includes(locale.url)
            if (!includes) {
              endpoints.push(locale.url)
            }
          }
        }
      }
    }
    return endpoints
  }
}

export default wppRouter

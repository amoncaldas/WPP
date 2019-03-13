import store from '@/store/store'

const wppRouter = {
  getSectionEndpoints: () => {
    let sectionsRoutes = store.getters.sectionsRoutes
    return sectionsRoutes || []
  },
  getSections: (includeHomes = true) => {
    let sections
    if (includeHomes) {
      sections = store.getters.sections
    } else {
      sections = []
      store.getters.sections.forEach(section => {
        if (section.link !== '/') {
          sections.push(section)
        }
      })
    }
    return sections || []
  },
  getPostTypeEndpoints: () => {
    let endpoints = []
    if (store.getters.options.post_type_endpoints) {
      endpoints = store.getters.options.post_type_endpoints

      for (let key in endpoints) {
        if (typeof endpoints[key] === 'string') {
          let declaredEndpoint = endpoints[key]
          wppRouter.addEndpointTranslations(declaredEndpoint, endpoints)
          endpoints[key] = {endpoint: declaredEndpoint, path: declaredEndpoint}
        }
      }
    }
    return endpoints
  },
  getPageLikeEndPoints: () => {
    let pageTypes = store.getters.options.page_like_types
    pageTypes = Array.isArray(pageTypes) ? pageTypes : [pageTypes]
    return pageTypes
  },

  addEndpointTranslations: (endpoint, endpoints) => {
    let translations = store.getters.options.post_type_translations
    if (translations) {
      for (let tKey in translations) {
        let translation = translations[tKey]

        let hasTranslation = wppRouter.endPointHasTranslations(endpoint, translation)
        if (hasTranslation) {
          for (let key in translation) {
            let locale = translation[key]
            let includes = endpoints.includes(locale.path)
            if (!includes) {
              endpoints.push({endpoint: endpoint, path: locale.path})
            }
          }
        }
      }
    }
    return endpoints
  },

  endPointHasTranslations: (endpoint, translation) => {
    let match = false
    for (let key in translation) {
      let locale = translation[key]
      if (locale.path === endpoint) {
        match = true
      }
    }
    return match
  },

  resolveDependencies: () => {
    return new Promise((resolve) => {
      let promise1 = store.dispatch('fetchSections')
      let promise2 = store.dispatch('fetchOptions')
      let promise3 = store.dispatch('autoSetLocale')
      Promise.all([promise1, promise2, promise3]).then((data) => {
        resolve(data)
      })
    })
  }
}

export default wppRouter

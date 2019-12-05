import store from '@/store/store'
import Main from '@/main'

const section = {
  getListingPosts () {
    let listPostEndpoints = []
    let currentSection = section.getCurrentSection()
    let VueInstance = Main.getInstance()

    if (currentSection && Array.isArray(currentSection.extra.list_post_endpoints)) {
      let translations = store.getters.options.post_type_translations

      currentSection.extra.list_post_endpoints.forEach(endpoint => {
        let localesTranslation = VueInstance.lodash.find(translations, (locales) => {
          return VueInstance.lodash.find(locales, locale => {
            return locale.path === endpoint
          })
        })
        if (localesTranslation) {
          let translation = localesTranslation[store.getters.locale]
          listPostEndpoints.push({endpoint: endpoint, title: translation.title})
        } else {
          listPostEndpoints.push({endpoint: endpoint, title: endpoint})
        }
      })
    }
    return listPostEndpoints
  },
  getCompactListingPosts () {
    let compactListPostEndpoints = []
    let currentSection = section.getCurrentSection()
    let VueInstance = Main.getInstance()

    if (currentSection && Array.isArray(currentSection.extra.compact_list_post_endpoints)) {
      let translations = store.getters.options.post_type_translations

      currentSection.extra.compact_list_post_endpoints.forEach(endpoint => {
        let localesTranslation = VueInstance.lodash.find(translations, (locales) => {
          return VueInstance.lodash.find(locales, locale => {
            return locale.path === endpoint
          })
        })
        if (localesTranslation) {
          let translation = localesTranslation[store.getters.locale]
          compactListPostEndpoints.push({endpoint: endpoint, title: translation.title})
        } else {
          compactListPostEndpoints.push({endpoint: endpoint, title: endpoint})
        }
      })
    }
    return compactListPostEndpoints
  },
  getCurrentSection () {
    let VueInstance = Main.getInstance()
    if (!VueInstance) {
      return store.getters.currentSection
    } else {
      let currentFirstPathSegment = location.pathname.replace(/^\/+/g, '')
      if (currentFirstPathSegment.indexOf('/') > 0) {
        currentFirstPathSegment = currentFirstPathSegment.split('/')[0]
      }
      currentFirstPathSegment = `/${currentFirstPathSegment}`
      let currentSection = VueInstance.lodash.find(store.getters.sections, (section) => {
        return section.path === currentFirstPathSegment && (section.path !== '/' || section.locale === store.getters.locale)
      })
      if (!currentSection) {
        currentSection = section.getCurrentHomeSection()
      }
      return currentSection
    }
  },
  getCurrentHomeSection () {
    let VueInstance = Main.getInstance()
    let currentHomeSection = VueInstance.lodash.find(store.getters.sections, (section) => {
      return section.path === '/' && section.locale === store.getters.locale
    })
    return currentHomeSection
  },

  getSectionById (id) {
    let VueInstance = Main.getInstance()
    let section = VueInstance.lodash.find(store.getters.sections, (section) => {
      return section.id === id
    })
    return section
  }
}

export default section

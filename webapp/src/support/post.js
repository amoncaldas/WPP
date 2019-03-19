import store from '@/store/store'
import Main from '@/main'

const post = {
  getListingPosts (post) {
    let listPostEndpoints = []
    let VueInstance = Main.getInstance()

    if (Array.isArray(post.extra.sidebar_post_types)) {
      let translations = store.getters.options.post_type_translations

      post.extra.sidebar_post_types.forEach(endpoint => {
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
  }
}

export default post

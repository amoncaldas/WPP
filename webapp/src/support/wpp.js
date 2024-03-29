import store from '@/store/store'
import lodash from 'lodash'

const logoUrl = () => {
  let url = store.getters.options.site_relative_logo_url.trim()

  if (store.getters.currentSection && store.getters.currentSection.extra.logo_url) {
    url = store.getters.currentSection.extra.logo_url.trim()
  }
  return url
}
/**
 * Get the current post type title translated according the current locale
 * @returns {String} title
 */
const getArchiveTranslated = (archiveType) => {
  let translations = store.getters.options.post_type_translations
  let translation = archiveType || store.getters.postTypeEndpoint
  let localesTranslation = lodash.find(translations, (locales) => {
    return lodash.find(locales, locale => {
      return locale.path === store.getters.postTypeEndpoint
    })
  })
  if (localesTranslation) {
    let matchTranslation = localesTranslation[store.getters.locale]
    translation = matchTranslation.title
  }
  return translation
}

/**
 * Get the post type based on the endpoint
 * @param endpoint
 * @returns {String} post_type (key)
 */
const getPostTypeFromEndpoint = (endpoint) => {
  for (let key in store.getters.options.post_type_translations) {
    const translation = store.getters.options.post_type_translations[key][store.getters.locale]
    if (translation.path === endpoint) {
      return key
    }
  }
}
/**
 * Get the current endpoint translated according the current locale
 * @returns {String} path
 */
const getCurrentEndpointTranslated = () => {
  let translations = store.getters.options.post_type_translations
  let translation = store.getters.postTypeEndpoint
  let localesTranslation = lodash.find(translations, (locales) => {
    return lodash.find(locales, locale => {
      return locale.path === store.getters.postTypeEndpoint
    })
  })
  if (localesTranslation) {
    let matchTranslation = localesTranslation[store.getters.locale]
    translation = matchTranslation.path
  }
  return translation
}
export default {
  logoUrl,
  getArchiveTranslated,
  getCurrentEndpointTranslated,
  getPostTypeFromEndpoint
}

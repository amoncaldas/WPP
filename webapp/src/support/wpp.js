import store from '@/store/store'
import main from '@/main'

const logoUrl = () => {
  let url = store.getters.options.site_relative_logo_url.trim()

  if (store.getters.currentSection && store.getters.currentSection.extra.logo_url) {
    url = store.getters.currentSection.extra.logo_url.trim()
  }
  return url
}
const getArchiveTranslated = () => {
  let translations = store.getters.options.post_type_translations
  let context = main.getInstance()
  let translation = store.getters.postTypeEndpoint
  let localesTranslation = context.lodash.find(translations, (locales) => {
    return context.lodash.find(locales, locale => {
      return locale.path === store.getters.postTypeEndpoint
    })
  })
  if (localesTranslation) {
    let matchTranslation = localesTranslation[context.$store.getters.locale]
    translation = matchTranslation.title
  }
  return translation
}
export default {
  logoUrl,
  getArchiveTranslated
}

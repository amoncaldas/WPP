import store from '@/store/store'

const logoUrl = () => {
  let url = store.getters.options.site_relative_logo_url.trim()

  if (store.getters.currentSection && store.getters.currentSection.extra.logo_url) {
    url = store.getters.currentSection.extra.logo_url.trim()
  }
  return url
}
export default {
  logoUrl
}

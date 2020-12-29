import store from '@/store/store'
import main from '@/main'

const crudHttpOptions = {
  baseURL: 'https://fam.eco/wp-json', // String, an empty string is the default,
  isAuthenticated: () => {
    return store.getters.isAuthenticated
  },
  getVueInstance: () => {
    let instance = main.getInstance()
    return instance
  },
  getBearerToken: () => {
    return store.getters.user.token
  },
  geLocale: () => {
    return store.getters.locale
  },
  appendLocaleToHeader: true,
  appendLocaleToGetUrl: true,
  urlLocalKey: 'l'
}
export default crudHttpOptions

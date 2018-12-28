import axios from 'axios'
import appConfig from '@/config'
import VueInstance from '@/main'

let baseURL = appConfig.getBaseUrl()

const httpApi = axios.create({
  baseURL: baseURL,
  headers: {
  }
})

/**
 * Modifies the request before it is sent
 *
 * @param {} config
 */
const requestInterceptors = (config) => {
  // if yes, show the loading and add the authorization header
  VueInstance.eventBus.$emit('showLoading', true)

  // Set/increase the pending request counter
  VueInstance.$pendingRequest = VueInstance.$pendingRequest ? VueInstance.$pendingRequest + 1 : 1

  // Before each request, we check if the user is authenticated
  // This store isAuthenticated getter relies on the @/common/auth/auth.store.js module
  if (VueInstance.$store.getters.isAuthenticated) {
    config.headers.common['Authorization'] = 'Bearer ' + VueInstance.$store.getters.user.token
  }
  return config // you have to return the config, otherwise the request wil be blocked
}

/**
 * Modifies the response after it is returned
 * @param {*} response
 */
const responseInterceptors = (response) => {
  // Decrease the pending request counter
  VueInstance.$pendingRequest--

  // If the the pending request counter is zero, so
  // we can hide the progress bar
  if (VueInstance.$pendingRequest === 0) {
    VueInstance.eventBus.$emit('showLoading', false)
  }
  response = response.response || response
  response.data = response.data || {}
  return response
}

/**
 * Modifies the error/fail response after it is finished
 * @param {*} response
 */
const responseErrorInterceptors = (response) => {
  return new Promise((resolve, reject) => {
    // Decrease the pending request counter
    VueInstance.$pendingRequest--

    // If the the pending request counter is zero, so
    // we can hide the progress bar
    if (VueInstance.$pendingRequest === 0) {
      VueInstance.eventBus.$emit('showLoading', false)
    }
    response = response.response || response
    response.data = response.data || {}
    reject(response)
  })
}

httpApi.interceptors.request.use(requestInterceptors)
httpApi.interceptors.response.use(responseInterceptors, responseErrorInterceptors)

export default httpApi

import axios from 'axios'
import appConfig from '@/config'
import VueInstance from '@/main'
import store from '@/store/store'

let env = process.env
let baseURL = env.NODE_ENV === 'production' ? appConfig.prodBaseAdminAjaxUrl : appConfig.devBaseAdminAjaxUrl

const httpAdminAjax = axios.create({
  baseURL: baseURL,
  headers: {
  }
})

/**
 * Enable the loading when request starts
 *
 * @param {} config
 */
const requestInterceptors = (config) => {
  VueInstance.eventBus.$emit('showLoading', true)
  var user = store.getters.user
  if (user && user.token) {
    config.headers.common['Authorization'] = 'Bearer ' + user.token
  }
  return config // you have to return the config, otherwise the request wil be blocked
}

/**
 * Stop the loading
 * @param {*} response
 */
const responseInterceptors = (response) => {
  VueInstance.eventBus.$emit('showLoading', false)
  response = response.response || response
  return response
}

/**
 * Stop the loading when response fail
 * @param {*} response
 */
const responseErrorInterceptors = (response) => {
  return new Promise((resolve, reject) => {
    VueInstance.eventBus.$emit('showLoading', false)
    response = response.response || response
    reject(response)
  })
}

httpAdminAjax.interceptors.request.use(requestInterceptors)
httpAdminAjax.interceptors.response.use(responseInterceptors, responseErrorInterceptors)

export default httpAdminAjax

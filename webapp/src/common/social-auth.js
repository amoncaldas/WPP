import Vue from 'vue'
import VueAxios from 'vue-axios'
import axios from 'axios'
import appConfig from '@/config'
import socialOauthData from '@/shared-services/social-oauth-data-service'
import store from '@/store/store'
import utils from '@/support/utils'
import socialOauthService from '@/shared-services/social-oauth-service'
import main from '@/main'

Vue.use(VueAxios, axios)
let baseUrl = appConfig.getBaseUrl()

const getRedirectUri = () => {
  return location.origin + location.pathname
}

/**
 * Here we define/enable the github social oauth for github
 */
const oAuthConfig = {
  baseUrl: baseUrl,
  providers: {
    github: {
      clientId: null, // to be set on authenticate, retrieving from bak-end
      redirectUri: getRedirectUri(),
      url: '/ors-oauth/github', // the back-end api endpoint where send to  the `code` and get the authentication result and data
      optionalUrlParams: ['scope'],
      scope: ['user:email'], // we need to the user email
      oauthBaseUrl: 'https://github.com/login/oauth/authorize',
      getOauthUrl: (clientId) => {
        let gh = oAuthConfig.providers.github
        return `${gh.oauthBaseUrl}?client_id=${gh.clientId}&redirect_uri=${gh.redirectUri}&response_type=code&scope=${gh.scope.join()}`
      }
    }
  }
}

/**
 * Authenticate a user using a social oauth provider
 *
 * @param context - the `this` context of the vue instance
 * @param provider - the identification of the provider, Currently only `github` is supported
 *
 * This authentication uses vue-authenticate @see https://github.com/dgrubelic/vue-authenticate
 * to login the user. The flow works as following:
 *
 * 1 - in @/main.js vue-authenticate is incorporated into the vue instance using Vue.use(...)
 * 2 - when socialAuth.authenticate is called, it will make a request to the back-end
 *    to retrieve the provider `clientId` and set it to the corresponding @/common/social-auth.js provider `clientId` property.
 * 3 - We call $auth.authenticate, that is a VueAuthenticate accessor. It opens the corresponding social provider login      *    pop-up, using the `clientId` defined in the previous step and allows the user to authenticate.
 *    When it is finished, the component will resolves the promise and brings back a temporary `code`.
 *    We send this `code` to the back-end and there, the `ors-oauth` plugin, using this code we will get a token and then we *    use this token to get the user data, including the user email registered on the provider.
 * 4 - Still on the back-end, having the user's email, we locate the user account on wordpress and generate a standard JWT token.
 * 5 - In the response, we give back the provider token (expected in the response by the vue-authenticate) and
 *    an additional property named `user`, where we put the JWT token and other user`s data. These data are passed
 *    back to who called the this authenticate on the resolver function and the normal authentication continues.
 */
const oauthViaPopUp = (context, provider) => {
  return new Promise((resolve, reject) => {
    // get the social oauth config data from back-end, provided by the `ors-oauth` plugin
    socialOauthData.query().then((response) => {
      if (response.raw && response.data) {
        response = response.data
      }
      // set the provider client id retrieved from the back-end
      oAuthConfig.providers[provider].clientId = response[provider].clientId

      // runs the VueAuthenticate authenticate method
      context.$auth.authenticate(provider).then((response) => {
        resolve(response.data.user)
      }, (error) => {
        reject(error)
      }).catch((error) => {
        reject(error)
      })
    })
  })
}

/**
 * Start oauth authentication via redirect to the provider authentication page
 * @param {*} provider
 */
const oauthViaRedirect = (provider, action) => {
  // to start the oauth process we have to clear the local storage
  // to make sure that we are not using old data
  localStorage.clear()

  // get the social oauth config data from back-end, provided by the `ors-oauth` plugin
  socialOauthData.query().then((response) => {
    if (response.raw) {
      response = response.data
    }
    store.commit('socialOauthAction', action)
    // set the provider client id retrieved from the back-end
    store.commit('socialOauthProvider', provider)
    oAuthConfig.providers[provider].clientId = response[provider].clientId
    let provConf = oAuthConfig.providers[provider]
    let oauthLocation = provConf.getOauthUrl()

    // redirect to the provider pge, where the user will authenticated and redirected back to our app
    window.location = oauthLocation
  })
}

/**
 * If the app is in the state of being called back after oauth login
 * store the transient code generated and present in the url and build
 * the forward url to which the app must be redirected
 * @return boolean if the flow must continue or not
 */
const runOauthCallBackCheck = () => {
  let params = utils.getUrlParams()
  if (params.code) {
    store.commit('socialOauthCode', params.code)
    store.dispatch('getSocialOauthAction').then((socialOauthAction) => {
      let hardRedirect = `${location.origin}${location.pathname}#/${socialOauthAction}`
      window.location = hardRedirect
    })
    return false
  } else {
    if (params.error_description) {
      main.getInstance().showError(params.error_description)
    }
    return true
  }
}

/**
 * On created it is checked if the transient oauth code is available on the store
 * and if yes, we proceed the oauth authentication.
 * This case will happen when a oauth process has started, the user was redirected to
 * the provider page and after authenticating s/he was redirected to here, so
 * we can complete the authentication process, sending the transient code to the back-end
 * and exchanging it by a valid JWT token and the user data
 *
 * @param context the VueJS this
 */
function checkAndProceedOAuth (callback) {
  let VueInstance = main.getInstance()
  authenticateUser().then((userData) => {
    if (userData) {
      localStorage.clear()
      VueInstance.showSuccess(VueInstance.$t('auth.authenticationWithOAuthCompleted'))
      callback(userData)
    }
  }, (error) => {
    if (typeof error === 'string' || error instanceof String) {
      VueInstance.showError(error, {mode: 'multi-line', timeout: 6000}) // a long message need more time to be read
    } else {
      console.log(error)
      VueInstance.showError(VueInstance.$t('auth.yourGithubAccountIsNotLinkedYet'), {mode: 'multi-line', timeout: 6000}) // a long message need more time to be read
    }
  })
}

/**
 * Get the oauth data based in the temp oauth code stored in the local storage
 *
 * @returns Promise
 */
function authenticateUser () {
  return new Promise((resolve, reject) => {
    store.dispatch('getSocialOauthCode').then((socialOauthCode) => {
      if (socialOauthCode) {
        let VueInstance = main.getInstance()
        VueInstance.showInfo(VueInstance.$t('auth.processingOAuth'), {timeout: 6000})
        store.commit('socialOauthCode', null) // remove the temp code from local storage
        buildEndPointAndProceedOauth(socialOauthCode, resolve, reject)
      } else {
        resolve()
      }
    })
  })
}

/**
 * Build the endpoint and proceed the oauth process
 * @param {*} socialOauthCode the temp code returned by the git oauth
 * @param {*} resolve the callback function if it succeed
 * @param {*} reject the callback function if it fails
 */
function buildEndPointAndProceedOauth (socialOauthCode, resolve, reject) {
  let options = {verb: 'post', data: { code: socialOauthCode }, raw: true}

  buildOauthEndPoint().then((endPoint) => {
    socialOauthService.customQuery(options, endPoint).then(response => {
      resolve(response.user)
    }, errorResponse => {
      if (errorResponse.data && errorResponse.data.message) {
        reject(errorResponse.data.message)
      } else {
        reject(errorResponse)
      }
    })
  })
}

/**
 * Build the oauth endpoint based on the provider and the action stored in the store
 * These values are in the store (and internally in the browser's local storage) because
 * we need to keep this across the oauth process that involves redirection to github oauth page
 * and then a redirect from github to our app again.
 */
function buildOauthEndPoint () {
  return new Promise((resolve, reject) => {
    let providerPromise = store.dispatch('getSocialOauthProvider')
    let actionPromise = store.dispatch('getSocialOauthAction')

    Promise.all([providerPromise, actionPromise]).then((results) => {
      let provider = results[0]
      let action = results[1]
      let endPoint = `${socialOauthService.getEndPoint()}${provider}/${action}`
      resolve(endPoint)
    })
  })
}

let socialAuth = {
  runOauthCallBackCheck,
  oAuthConfig, // consumed by the main to incorporate the VueAuthenticate on the vue instance
  oauthViaPopUp, // method that must be called to authenticate a user from a vue component
  oauthViaRedirect,
  checkAndProceedOAuth
}

export default socialAuth

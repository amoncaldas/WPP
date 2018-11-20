// import socialOauthService from '@/common//social-oauth-service'

const state = {
  token: null,
  displayName: null,
  userEmail: null,
  id: null
}

const getters = {
  user (state) {
    return {
      token: state.token,
      userEmail: state.userEmail,
      displayName: state.displayName,
      id: state.id
    }
  },
  isAuthenticated (state) {
    return (state.token != null && validToken() !== false)
  }
}

const mutations = {
  authUser: (state, userData) => {
    state.token = userData.token
    state.userEmail = userData.userEmail
    state.displayName = userData.displayName
    state.id = userData.id
  },
  userDisplayName: (state, userDisplayName) => {
    state.displayName = userDisplayName
  },
  clearAuthData: (state) => {
    state.token = null
    state.displayName = null
    state.userEmail = null
    state.id = null
  },
  socialOauthCode: (state, code) => {
   /**
   * We are using local storage because we need to keep this across the oauth process that involves redirection to github oauth page
   * and then a redirect from github to our app again.
   */
    if (code) {
      localStorage.setItem('socialOauthCode', code)
    } else {
      localStorage.removeItem('socialOauthCode')
    }
  },
  socialOauthProvider: (state, provider) => {
    /**
    * We are using local storage because we need to keep this across the oauth process that involves redirection to github oauth page
    * and then a redirect from github to our app again.
    */
    if (provider) {
      localStorage.setItem('socialOauthProvider', provider)
    } else {
      localStorage.removeItem('socialOauthProvider')
    }
  },
  socialOauthAction: (state, provider) => {
   /**
   * We are using local storage because we need to keep this across the oauth process that involves redirection to github oauth page
   * and then a redirect from github to our app again.
   */
    if (provider) {
      localStorage.setItem('socialOauthAction', provider)
    } else {
      localStorage.removeItem('socialOauthAction')
    }
  }
}

const actions = {
  setLogoutTimer ({commit}, expirationTime) {
    setTimeout(() => {
      commit('clearAuthData')
    }, expirationTime * 1000)
  },
  login ({commit, dispatch}, authData) {
    return new Promise((resolve, reject) => {
      const now = new Date()
      const expirationDate = new Date(now.getTime() + 3200 * 1000 * 8)

      localStorage.setItem('expiresAt', expirationDate)
      localStorage.setItem('token', authData.token)
      localStorage.setItem('displayName', authData.displayName)
      localStorage.setItem('userEmail', authData.userEmail)
      localStorage.setItem('userId', authData.id)

      commit('authUser', authData)
      dispatch('setLogoutTimer', 3200)
      resolve(authData)
    })
  },
  tryAutoLogin ({commit}) {
    return new Promise((resolve, reject) => {
      let token = validToken()
      if (token) {
        const userEmail = localStorage.getItem('userEmail')
        const displayName = localStorage.getItem('displayName')
        const id = localStorage.getItem('userId')
        commit('authUser', {
          token: token,
          displayName: displayName,
          userEmail: userEmail,
          id: id
        })
      }
      resolve()
    })
  },
  logout ({commit}) {
    return new Promise((resolve, reject) => {
      commit('clearAuthData')
      localStorage.removeItem('token')
      localStorage.removeItem('displayName')
      localStorage.removeItem('userEmail')
      localStorage.removeItem('userId')
      resolve()
    })
  },
  getSocialOauthCode ({commit}) {
    return new Promise((resolve, reject) => {
      let code = localStorage.getItem('socialOauthCode')
      code = code === 'undefined' ? null : code
      resolve(code)
    })
  },
  getSocialOauthProvider ({commit}) {
    return new Promise((resolve, reject) => {
      let provider = localStorage.getItem('socialOauthProvider')
      provider = provider === 'undefined' ? null : provider
      resolve(provider)
    })
  },
  getSocialOauthAction ({commit}) {
    return new Promise((resolve, reject) => {
      let provider = localStorage.getItem('socialOauthAction')
      provider = provider === 'undefined' ? null : provider
      resolve(provider)
    })
  }
}

/**
 * Validate the token from browser's local storage
 * To do so, it checks if the token exists and if has not expired
 */
function validToken () {
  const token = localStorage.getItem('token')
  const expiresAt = new Date(localStorage.getItem('expiresAt'))
  const now = new Date()
  if (token && expiresAt > now) {
    return token
  }
  return false
}

export default {
  state,
  getters,
  mutations,
  actions
}

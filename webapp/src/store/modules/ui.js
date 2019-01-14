import MainMenu from '@/common/main-menu'
import appConfig from '@/config'

const state = {
  leftSideBarOpen: false,
  mainMenu: [],
  locale: null
}

const getters = {
  leftSideBarOpen: state => {
    return state.leftSideBarOpen
  },
  mainMenu: state => {
    return state.mainMenu
  },
  locale: state => {
    return state.locale
  }
}

const mutations = {
  setLeftSideBarIsOpen: (state, isOpen) => {
    state.leftSideBarOpen = isOpen
  },
  mainMenu: (state, items) => {
    state.mainMenu = items
  },
  locale: (state, locale) => {
    state.locale = locale
  }
}

const actions = {
  fetchMainMenu ({getters, commit}) {
    return new Promise((resolve) => {
      if (getters.mainMenu.length > 0) {
        resolve(getters.mainMenu)
      }
      MainMenu.loadItems().then((items) => {
        commit('mainMenu', items)
        resolve(items)
      })
    })
  },
  autoSetLocale ({commit}) {
    return new Promise((resolve) => {
      let locale = localStorage.getItem('locale') || window.navigator.language || window.navigator.userLanguage
      let validLocale = locale && appConfig.validLocales.includes(locale)
      if (!validLocale) {
        locale = appConfig.defaultLocale
      }
      localStorage.setItem('locale', locale)
      commit('locale', locale)
      resolve(locale)
    })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}

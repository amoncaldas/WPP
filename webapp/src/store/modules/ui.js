import SiteMenu from '@/common/site-menu'
import appConfig from '@/config'

const state = {
  leftSideBarOpen: false,
  mainMenu: [],
  secondaryMenu: [],
  locale: null
}

const getters = {
  leftSideBarOpen: state => {
    return state.leftSideBarOpen
  },
  mainMenu: state => {
    return state.mainMenu
  },
  secondaryMenu: state => {
    return state.secondaryMenu
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
  secondaryMenu: (state, items) => {
    state.secondaryMenu = items
  },
  locale: (state, locale) => {
    state.locale = locale
    localStorage.setItem('locale', locale)
  }
}

const actions = {
  fetchMainMenu ({getters, commit}) {
    return new Promise((resolve) => {
      SiteMenu.loadItems(appConfig.mainMenuSlug).then((items) => {
        commit('mainMenu', items)
        resolve(items)
      })
    })
  },
  fetchSecondaryMenu ({getters, commit}) {
    return new Promise((resolve) => {
      SiteMenu.loadItems(appConfig.secondaryMenu).then((items) => {
        commit('secondaryMenu', items)
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

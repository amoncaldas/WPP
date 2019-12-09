import SiteMenu from '@/common/site-menu'
import appConfig from '@/config'

const state = {
  leftSideBarOpen: false,
  mainMenu: [],
  secondaryMenu: [],
  locale: null,
  defaultBackground: null,
  defaultTheme: null,
  isDark: null
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
  },
  defaultBackground: state => {
    return state.defaultBackground
  },
  defaultTheme: state => {
    return state.defaultTheme
  },
  isDark: state => {
    return state.isDark
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
  },
  defaultBackground: (state, value) => {
    state.defaultBackground = value
  },
  defaultTheme: (state, value) => {
    state.defaultTheme = value
  },
  isDark: (state, value) => {
    state.isDark = value
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
      if (locale) {
        locale = locale.toLowerCase()
      }
      let isLocaleValid = locale && appConfig.validLocales.includes(locale)
      if (!isLocaleValid) {
        // Check if the browser supports the app default locale
        // If supports, define it as the locale
        if (window.navigator.languages.includes(appConfig.defaultLocale)) {
          locale = appConfig.defaultLocale
        } else { // If not, try to use the english (if supported) or fall back to the default
          locale = appConfig.validLocales.includes('en-us') ? 'en-us' : appConfig.defaultLocale
        }
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

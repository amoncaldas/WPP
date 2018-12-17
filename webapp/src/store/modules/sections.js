import MainMenu from '@/common/main-menu'

const state = {
  leftSideBarOpen: false,
  mainMenu: []
}

const getters = {
  leftSideBarOpen: state => {
    return state.leftSideBarOpen
  },
  mainMenu: state => {
    return state.mainMenu
  }
}

const mutations = {
  setLeftSideBarIsOpen: (state, isOpen) => {
    state.leftSideBarOpen = isOpen
  },
  mainMenu: (state, items) => {
    state.mainMenu = items
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
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}

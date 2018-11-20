import MainMenu from '@/common/main-menu'

const state = {
  leftSideBarOpen: false,
  headMenu: [],
  sideMenu: []
}

const getters = {
  leftSideBarOpen: state => {
    return state.leftSideBarOpen
  },
  headMenu: state => {
    return state.headMenu
  },
  sideMenu: state => {
    return state.sideMenu
  }
}

const mutations = {
  setLeftSideBarIsOpen: (state, isOpen) => {
    state.leftSideBarOpen = isOpen
  },
  headMenu: (state, items) => {
    state.headMenu = items
  },
  sideMenu: (state, items) => {
    state.sideMenu = items
  }
}

const actions = {
  fetchHeadMenu ({commit, dispatch}) {
    return new Promise((resolve, reject) => {
      MainMenu.loadItems().then((items) => {
        commit('headMenu', items)
        resolve(items)
      })
    })
  },
  fetchSideMenu ({commit, dispatch}) {
    return new Promise((resolve, reject) => {
      // We are using the same item from main menu on side bar,
      // but we could use another menu here
      MainMenu.loadItems().then((items) => {
        commit('sideMenu', items)
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

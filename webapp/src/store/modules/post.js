const state = {
  postTypeEndpoint: null
}

const getters = {
  postTypeEndpoint: state => {
    return state.postTypeEndpoint
  }
}

const mutations = {
  postTypeEndpoint: (state, postTypeEndpoint) => {
    state.postTypeEndpoint = postTypeEndpoint
  }
}

const actions = {
}

export default {
  state,
  getters,
  mutations,
  actions
}

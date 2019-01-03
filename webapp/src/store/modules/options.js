import optionService from '@/shared-services/option-service'

const state = {
  options: []
}

const getters = {
  options: state => {
    return state.options
  }
}

const mutations = {
  options: (state, items) => {
    state.options = items
  }
}

const actions = {
  fetchOptions ({getters, commit}) {
    return new Promise((resolve) => {
      if (getters.options.length > 0) {
        resolve(getters.options)
      }
      optionService.query().then((options) => {
        commit('options', options)
        resolve(options)
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

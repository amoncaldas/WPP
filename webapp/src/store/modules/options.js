import VueRestCrud from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

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
      let vueRestCrud = new VueRestCrud.CrudHttpApi(CrudHttpOptions)
      vueRestCrud.http.get('wpp/v1/services/options').then((response) => {
        let options = response.data
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

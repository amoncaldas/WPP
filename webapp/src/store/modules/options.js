import {HttpClient} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

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
      let httpClient = new HttpClient(HttpClientOptions)
      httpClient.http.get('wpp/v1/services/options').then((response) => {
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

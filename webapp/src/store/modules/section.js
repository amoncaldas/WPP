import sectionService from '@/shared-services/section-service'

const state = {
  sections: []
}

const getters = {
  sections: state => {
    return state.sections
  }
}

const mutations = {
  sections: (state, items) => {
    state.sections = items
  }
}

const actions = {
  fetchSections ({getters, commit}) {
    return new Promise((resolve) => {
      if (getters.sections.length > 0) {
        resolve(getters.sections)
      }
      sectionService.query().then((sections) => {
        commit('sections', sections)
        resolve(sections)
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

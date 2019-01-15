import httpApi from '@/common/http-api'
import appConfig from '@/config'

const state = {
  sections: [],
  sectionsRoutes: []
}

const getters = {
  sections: state => {
    return state.sections
  },
  sectionsRoutes: state => {
    return state.sectionsRoutes
  }
}

const mutations = {
  sections: (state, items) => {
    state.sections = items
  },
  sectionsRoutes: (state, items) => {
    state.sectionsRoutes = items
  }
}

const actions = {
  fetchSections ({getters, commit}) {
    return new Promise((resolve) => {
      if (getters.sections.length > 0) {
        resolve(getters.sections)
      }
      httpApi.get(appConfig.baseWpApiPath + 'sections?_embed').then((response) => {
        let sections = response.data
        commit('sections', sections)

        let sectionsRoutes = []
        var parser = document.createElement('a')
        sections.forEach(section => {
          parser.href = section.link
          if (parser.pathname !== '/' && parser.pathname !== '' && sectionsRoutes.indexOf(parser.pathname) === -1) {
            sectionsRoutes.push(parser.pathname)
          }
        })
        commit('sectionsRoutes', sectionsRoutes)
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

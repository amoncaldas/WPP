import httpApi from '@/common/http-api'
import appConfig from '@/config'

const state = {
  sections: [],
  sectionsRoutes: [],
  currentSection: null
}

const getters = {
  sections: state => {
    return state.sections
  },
  sectionsRoutes: state => {
    return state.sectionsRoutes
  },
  currentSection: state => {
    return state.currentSection
  }
}

const mutations = {
  sections: (state, items) => {
    state.sections = items
  },
  sectionsRoutes: (state, items) => {
    state.sectionsRoutes = items
  },
  currentSection: (state, currentSection) => {
    state.currentSection = currentSection
  }
}

const actions = {
  fetchSections ({getters, commit}) {
    return new Promise((resolve) => {
      if (getters.sections.length > 0) {
        resolve(getters.sections)
      }
      httpApi.get(appConfig.baseWpApiPath + 'sections?_embed').then((response) => {
        var parser = document.createElement('a')
        let sections = response.data
        var regex = new RegExp('/', 'g')
        for (let key in sections) {
          let section = sections[key]
          parser.href = section.link
          sections[key].path = `/${parser.pathname.replace(regex, '')}`
          sections[key].extra = sections[key].acf
        }

        commit('sections', sections)

        let sectionsRoutes = []
        sections.forEach(section => {
          parser.href = section.link
          if (section.link !== '/' && section.link !== '' && sectionsRoutes.indexOf(section.link) === -1) {
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

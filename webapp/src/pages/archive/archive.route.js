import Archive from './Archive'
import wppRouter from '@/support/wpp-router'
import store from '@/store/store'
import Section from '@/support/section'

const routes = {
  get: () => {
    let routes = []

    let postTypeEndpoints = wppRouter.getPostTypeEndpoints()
    var regex = new RegExp('/', 'g')

    postTypeEndpoints.forEach(postTypeEndpoint => {
      let postTypeEndpointUrl = postTypeEndpoint.path.replace(regex, '')
      let archiveHasASection = isArchiveOverwrittenBySectionSingle(postTypeEndpointUrl)
      if (!archiveHasASection) {
        routes.push(
          {
            path: `/${postTypeEndpointUrl}`,
            component: Archive,
            meta: {archive: true},
            beforeEnter: (to, from, next) => {
              let currentSection = Section.getCurrentSection()
              store.commit('currentSection', currentSection)
              store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
              next()
            }
          }
        )
        let sections = wppRouter.getSections(false)

        sections.forEach(section => {
          let sectionEndPoint = section.path.replace(regex, '')
          if (sectionEndPoint && sectionEndPoint !== '') {
            routes.push(
              {
                path: `/${sectionEndPoint}/${postTypeEndpointUrl}`,
                component: Archive,
                meta: {archive: true},
                beforeEnter: (to, from, next) => {
                  store.commit('currentSection', section)
                  store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
                  next()
                }
              }
            )
          }
        })
      }
    })
    return routes
  }
}

const isArchiveOverwrittenBySectionSingle = (endpoint) => {
  var regex = new RegExp('/', 'g')
  for (let key in store.getters.sections) {
    let section = store.getters.sections[key]
    let sectionEndPoint = section.path.replace(regex, '')
    if (sectionEndPoint === endpoint) {
      return true
    }
  }
  return false
}

const archiveRoutes = routes.get()

export default archiveRoutes

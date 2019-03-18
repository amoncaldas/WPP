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
      routes.push(
        {
          path: `/${postTypeEndpointUrl}`,
          name: `${postTypeEndpointUrl}-Archive`,
          component: Archive,
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
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpointUrl}`,
            name: `${sectionEndPoint}-${postTypeEndpointUrl}-Archive`,
            component: Archive,
            beforeEnter: (to, from, next) => {
              store.commit('currentSection', section)
              store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
              next()
            }
          }
        )
      })
    })
    return routes
  }
}

const archiveRoutes = routes.get()

export default archiveRoutes

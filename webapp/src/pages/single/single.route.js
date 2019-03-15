import Single from './Single'
import wppRouter from '@/support/wpp-router'
import store from '@/store/store'
import Section from '@/support/section'

const routes = {
  get: () => {
    let routes = []
    var regex = new RegExp('/', 'g')

    let postTypeEndpoints = wppRouter.getPostTypeEndpoints()
    postTypeEndpoints.forEach(postTypeEndpoint => {
      let postTypeEndpointUrl = postTypeEndpoint.path.replace(regex, '')
      routes.push(
        {
          path: `/${postTypeEndpointUrl}/:postName/:postId`,
          name: `${postTypeEndpointUrl}-Single`,
          component: Single,
          beforeEnter: (to, from, next) => {
            let currentSection = Section.getCurrentSection()
            store.commit('currentSection', currentSection)
            store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
            next()
          }
        }
      )
      routes.push(
        {
          path: `/${postTypeEndpointUrl}/:postParent1/:postId`,
          name: `${postTypeEndpointUrl}-Single`,
          component: Single,
          beforeEnter: (to, from, next) => {
            let currentSection = Section.getCurrentSection()
            store.commit('currentSection', currentSection)
            store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
            next()
          }
        }
      )
      routes.push(
        {
          path: `/${postTypeEndpointUrl}/:postParent1/:postName/:postId`,
          name: `${postTypeEndpointUrl}-Single-with-one-parent`,
          component: Single,
          beforeEnter: (to, from, next) => {
            let currentSection = Section.getCurrentSection()
            store.commit('currentSection', currentSection)
            store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
            next()
          }
        }
      )
      routes.push(
        {
          path: `/${postTypeEndpointUrl}/:postParent1/:postParent2/:postName/:postId`,
          name: `${postTypeEndpointUrl}-Single-with-two-parents`,
          component: Single,
          beforeEnter: (to, from, next) => {
            let currentSection = Section.getCurrentSection()
            store.commit('currentSection', currentSection)
            store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
            next()
          }
        }
      )
      routes.push(
        {
          path: `/${postTypeEndpointUrl}/:postId`,
          name: `${postTypeEndpointUrl}-SingleId`,
          component: Single,
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
            path: `/${sectionEndPoint}/${postTypeEndpointUrl}/:postName/:postId`,
            name: `${sectionEndPoint}-${postTypeEndpointUrl}-Single`,
            component: Single,
            beforeEnter: (to, from, next) => {
              store.commit('currentSection', section)
              store.commit('postTypeEndpoint', postTypeEndpoint.endpoint)
              next()
            }
          }
        )
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpointUrl}/:postId`,
            name: `${sectionEndPoint}-${postTypeEndpointUrl}-SingleId`,
            component: Single,
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

const singleRoutes = routes.get()

export default singleRoutes

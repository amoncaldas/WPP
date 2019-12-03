import Page from './Page'
import wppRouter from '@/support/wpp-router'
import store from '@/store/store'
import Section from '@/support/section'

const routes = {
  get: () => {
    let routes = []
    var regex = new RegExp('/', 'g')

    let isInSection = false

    for (let key in store.getters.sections) {
      let section = store.getters.sections[key]
      if (section.path !== '/' && location.pathname.startsWith(section.path)) {
        isInSection = true
        break
      }
    }

    let locationParts = location.pathname.split('/')
    let lastPathPart = locationParts[locationParts.length - 1]

    //  Page routes never ends with a last path parameter being a numbers
    if (isNaN(lastPathPart)) {
      if (!isInSection) {
        routes.push(
          {
            path: `/:postName/`,
            component: Page,
            meta: {page: true},
            beforeEnter: (to, from, next) => {
              let currentSection = Section.getCurrentHomeSection()
              store.commit('currentSection', currentSection)
              store.commit('postTypeEndpoint', 'pages')
              next()
            }
          }
        )
        routes.push(
          {
            path: `/:parentPage/:postName/`,
            component: Page,
            meta: {page: true},
            beforeEnter: (to, from, next) => {
              let currentSection = Section.getCurrentHomeSection()
              store.commit('currentSection', currentSection)
              store.commit('postTypeEndpoint', 'pages')
              next()
            }
          }
        )
      }

      let sectionEndPoints = wppRouter.getSectionEndpoints()
      sectionEndPoints.forEach(sectionEndPoint => {
        sectionEndPoint = sectionEndPoint.replace(regex, '')
        if (sectionEndPoint && sectionEndPoint !== '') {
          routes.push(
            {
              path: `/${sectionEndPoint}/:postName`,
              component: Page,
              meta: {page: true},
              beforeEnter: (to, from, next) => {
                let currentSection = Section.getCurrentSection()
                store.commit('currentSection', currentSection)
                store.commit('postTypeEndpoint', 'pages')
                next()
              }
            }
          )
          routes.push(
            {
              path: `/${sectionEndPoint}/:parentPage/:postName`,
              component: Page,
              meta: {page: true},
              beforeEnter: (to, from, next) => {
                let currentSection = Section.getCurrentSection()
                store.commit('currentSection', currentSection)
                store.commit('postTypeEndpoint', 'pages')
                next()
              }
            }
          )
        }
      })
    }

    return routes
  }
}

const pageRoutes = routes.get()

export default pageRoutes

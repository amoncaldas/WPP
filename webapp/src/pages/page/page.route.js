import Page from './Page'
import wppRouter from '@/support/wpp-router'
import store from '@/store/store'
import Section from '@/support/section'

const routes = {
  get: () => {
    let routes = []

    let sectionEndPoints = wppRouter.getSectionEndpoints()
    sectionEndPoints.forEach(sectionEndPoint => {
      routes.push(
        {
          path: `/:postName/`,
          name: 'Page',
          component: Page,
          beforeEnter: (to, from, next) => {
            let currentSection = Section.getCurrentSection()
            store.commit('currentSection', currentSection)
            store.commit('postTypeEndpoint', 'page')
            next()
          }
        }
      )
      routes.push(
        {
          path: `/${sectionEndPoint}/:postName`,
          name: 'Page',
          component: Page,
          beforeEnter: (to, from, next) => {
            let currentSection = Section.getCurrentSection()
            store.commit('currentSection', currentSection)
            store.commit('postTypeEndpoint', 'page')
            next()
          }
        }
      )
    })
  }
}

const singleRoutes = routes.get()

export default singleRoutes

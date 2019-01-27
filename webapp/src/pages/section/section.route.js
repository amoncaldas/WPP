import Section from './Section'
import wppRouter from '@/support/wpp-router'
import store from '@/store/store'

const routes = {
  get: () => {
    let routes = []
    var regex = new RegExp('/', 'g')
    let sections = wppRouter.getSections(false)

    sections.forEach(section => {
      let sectionEndPoint = section.link.replace(regex, '')
      routes.push(
        {
          path: `/${sectionEndPoint}`,
          name: `${sectionEndPoint}-Section`,
          component: Section,
          beforeEnter: (to, from, next) => {
            store.commit('currentSection', section)
            next()
          }
        }
      )
    })

    return routes
  }
}

const sectionRoutes = routes.get()

export default sectionRoutes

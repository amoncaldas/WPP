import Page from './Page'
import wppRouter from '@/support/wpp-router'

const routes = {
  get: () => {
    let routes = []

    let sectionEndPoints = wppRouter.getSectionEndpoints()
    sectionEndPoints.forEach(sectionEndPoint => {
      routes.push(
        {
          path: `/${sectionEndPoint}/:postName/:postId`,
          name: 'Page',
          component: Page
        }
      )
      routes.push(
        {
          path: `/${sectionEndPoint}/:postName`,
          name: 'Page',
          component: Page
        }
      )
    })
  }
}

const singleRoutes = routes.get()

export default singleRoutes

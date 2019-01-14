import Single from './Single'
import wppRouter from '@/support/wpp-router'

const routes = {
  get: () => {
    let routes = []

    let postTypeEndpoints = wppRouter.getPostTypeEndpoints()
    postTypeEndpoints.forEach(postTypeEndpoint => {
      routes.push(
        {
          path: `/${postTypeEndpoint}/:postName/:postId`,
          name: 'Single',
          component: Single
        }
      )
      routes.push(
        {
          path: `/${postTypeEndpoint}/:postId`,
          name: 'Single',
          component: Single
        }
      )
      let sectionEndPoints = wppRouter.getSectionEndpoints()
      sectionEndPoints.forEach(sectionEndPoint => {
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpoint}/:postName/:postId`,
            name: 'Archive',
            component: Single
          }
        )
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpoint}/:postId`,
            name: 'Archive',
            component: Single
          }
        )
      })
    })
  }
}

const singleRoutes = routes.get()

export default singleRoutes

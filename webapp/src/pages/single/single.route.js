import Single from './Single'
import wppRouter from '@/support/wpp-router'

const routes = {
  get: () => {
    let routes = []
    var regex = new RegExp('/', 'g')

    let postTypeEndpoints = wppRouter.getPostTypeEndpoints()
    postTypeEndpoints.forEach(postTypeEndpoint => {
      postTypeEndpoint = postTypeEndpoint.replace(regex, '')
      routes.push(
        {
          path: `/${postTypeEndpoint}/:postName/:postId`,
          name: `${postTypeEndpoint}-Single`,
          component: Single
        }
      )
      routes.push(
        {
          path: `/${postTypeEndpoint}/:postId`,
          name: `${postTypeEndpoint}-SingleId`,
          component: Single
        }
      )
      let sectionEndPoints = wppRouter.getSectionEndpoints()
      sectionEndPoints.forEach(sectionEndPoint => {
        sectionEndPoint = sectionEndPoint.replace(regex, '')
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpoint}/:postName/:postId`,
            name: `${sectionEndPoint}-${postTypeEndpoint}-Single`,
            component: Single
          }
        )
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpoint}/:postId`,
            name: `${sectionEndPoint}-${postTypeEndpoint}-SingleId`,
            component: Single
          }
        )
      })
    })
    return routes
  }
}

const singleRoutes = routes.get()

export default singleRoutes

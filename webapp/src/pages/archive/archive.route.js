import Archive from './Archive'
import wppRouter from '@/support/wpp-router'

const routes = {
  get: () => {
    let routes = []

    let postTypeEndpoints = wppRouter.getPostTypeEndpoints()
    var regex = new RegExp('/', 'g')

    postTypeEndpoints.forEach(postTypeEndpoint => {
      postTypeEndpoint = postTypeEndpoint.replace(regex, '')
      routes.push(
        {
          path: `/${postTypeEndpoint}`,
          name: `${postTypeEndpoint}-Archive`,
          component: Archive
        }
      )
      let sectionEndpoints = wppRouter.getSectionEndpoints()

      sectionEndpoints.forEach(sectionEndPoint => {
        sectionEndPoint = sectionEndPoint.replace(regex, '')
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpoint}`,
            name: `${sectionEndPoint}-${postTypeEndpoint}-Archive`,
            component: Archive
          }
        )
      })
    })
    return routes
  }
}

const archiveRoutes = routes.get()

export default archiveRoutes

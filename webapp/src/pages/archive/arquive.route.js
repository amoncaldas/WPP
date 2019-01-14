import Archive from './Archive'
import wppRouter from '@/support/wpp-router'

const routes = {
  get: () => {
    let routes = []

    let postTypeEndpoints = wppRouter.getPostTypeEndpoints()
    postTypeEndpoints.forEach(postTypeEndpoint => {
      routes.push(
        {
          path: `/${postTypeEndpoint}`,
          name: 'Archive',
          component: Archive
        }
      )
      wppRouter.getSectionEndpoints().forEach(sectionEndPoint => {
        routes.push(
          {
            path: `/${sectionEndPoint}/${postTypeEndpoint}`,
            name: 'Archive',
            component: Archive
          }
        )
      })
    })
  }
}

const archiveRoutes = routes.get()

export default archiveRoutes

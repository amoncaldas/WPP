import Home from '@/pages/home/Home'
import wppRouter from '@/support/wpp-router'

const routes = {
  get: () => {
    let routes = [{
      path: '/',
      name: 'Home',
      component: Home
    }]
    let sectionEndPoints = wppRouter.getSectionEndpoints()
    sectionEndPoints.forEach(sectionEndPoint => {
      routes.push(
        {
          path: `/${sectionEndPoint}`,
          name: 'Home',
          component: Home
        }
      )
    })
  }
}

const sectionHomeRoutes = routes.get()

export default sectionHomeRoutes

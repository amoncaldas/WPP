import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store/store'
import loader from '@/support/loader'
import socialAuth from '@/common/social-auth'
import HomeOrSearch from '@/pages/home-or-search/HomeOrSearch'

Vue.use(Router)

const getRouterMode = () => {
  let mode = store.getters.options.router_mode || 'hash'
  return mode
}

const getRouter = () => {
  let router = new Router({
    mode: getRouterMode(),
    // Initially the routes ARRAY is declared only with
    // the root/abstract route. It is gonna be populated below.
    // The route `/` will be matched whenever the app loads any route
    // because it is an abstract route, so every route contains the base `/` route
    routes: [ {
      path: '/',
      name: 'HomeOrSearch',
      component: HomeOrSearch,
      beforeEnter: (to, from, next) => {
        // Get current route
        // this only works in we are using the `hash` mode
        let route = location.hash.replace('#', '')

        // If the current route is the root `/` page
        // send the user to the home page
        // the `/home` route guard will check if the user is not logged in
        // if s/he is not, it will redirect the user to the `/login` page
        if (route === '/') {
          // if we are dealing with a oauth callback request,
          // we will run the the oauth routine and receive
          // a boolean if the flow most continue or not.
          let proceed = socialAuth.runOauthCallBackCheck()
          if (proceed) {
            next()
          }
        } else {
          // if the target is not the root `/` page
          // send the user to the target page
          next()
        }
      }
    }]
  })

  /**
  * We have to load the menu before entering in each route
  */
  router.beforeEach((to, from, next) => {
    let promise1 = store.dispatch('tryAutoLogin')

    Promise.all([promise1]).then(() => {
      next()
    })
  })

  router.loadRoutes = () => {
    // load and get all routes from components with name following the pattern *.route.js
    let routes = loader.load(require.context('@/pages/', true, /\.route\.js$/))

    // Once we have all additional routes, we add them to the router

    // Register first the single routes returns, because
    // they represent, usually, routes to static components
    // like `profile`, `register`, `logout` and ect.
    routes.forEach(componentRoute => {
      if (componentRoute) {
        if (!Array.isArray(componentRoute)) {
          router.addRoutes([componentRoute]) // as it is a single route, transform it in an array
        }
      }
    })

    // Then register the array of routes returned by
    // the components, that are routes build dynamically
    // based on values returned by the back-end
    routes.forEach(componentRoute => {
      if (componentRoute) {
        if (Array.isArray(componentRoute)) {
          router.addRoutes(componentRoute)
        }
      }
    })
  }
  return router
}

export default {
  getRouter
}

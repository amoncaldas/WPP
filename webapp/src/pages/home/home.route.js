import Home from '@/pages/home/Home'
import store from '@/store/store'

export default {
  path: '/home',
  name: 'Home',
  component: Home,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next()
    } else {
      next('/login')
    }
  }
}

import Home from '@/pages/home/Home'
import store from '@/store/store'

export default {
  path: '/profile',
  name: 'Profile',
  component: Home,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next()
    } else {
      next('/login')
    }
  }
}

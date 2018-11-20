import Auth from '@/pages/auth/Auth'
import store from '@/store/store'

export default {
  path: '/login',
  name: 'Login',
  component: Auth,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next('/home')
    } else {
      next()
    }
  }
}

import Login from '@/pages/login/Login'
import store from '@/store/store'

export default {
  path: '/login',
  name: 'Login',
  component: Login,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next('/profile')
    } else {
      next()
    }
  }
}

import ResetPassword from '@/pages/password/reset/Reset'
import store from '@/store/store'

export default {
  path: '/password/reset/:key/:login',
  name: 'PasswordReset',
  component: ResetPassword,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next({name: 'Home'})
    } else {
      next()
    }
  }
}

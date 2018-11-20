import PasswordRequest from '@/pages/password/request/Request'
import store from '@/store/store'

export default {
  path: '/password/request',
  name: 'PasswordRequest',
  component: PasswordRequest,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next({name: 'Home'})
    } else {
      next()
    }
  }
}

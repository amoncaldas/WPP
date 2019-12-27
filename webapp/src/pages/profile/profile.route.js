import Profile from '@/pages/profile/Profile'
import store from '@/store/store'

export default {
  path: '/profile',
  name: 'Profile',
  component: Profile,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next()
    } else {
      next('/login')
    }
  }
}

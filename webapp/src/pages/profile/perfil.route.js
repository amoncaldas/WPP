import Profile from '@/pages/profile/Profile'
import store from '@/store/store'

export default {
  path: '/perfil',
  name: 'Perfil',
  component: Profile,
  beforeEnter: (to, from, next) => {
    store.commit('locale', 'pt-br')
    if (store.getters.isAuthenticated) {
      next()
    } else {
      next('/login')
    }
  }
}

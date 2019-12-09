import Register from '@/pages/register/Register'
import store from '@/store/store'

export default {
  path: '/registro',
  name: 'Registro',
  component: Register,
  beforeEnter: (to, from, next) => {
    store.commit('locale', 'en-us')
    next()
  }
}

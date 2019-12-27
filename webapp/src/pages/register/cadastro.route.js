import Register from '@/pages/register/Register'
import store from '@/store/store'

export default {
  path: '/cadastro',
  name: 'Cadastro',
  component: Register,
  beforeEnter: (to, from, next) => {
    store.commit('locale', 'pt-br')
    next({path: '/register', query: {l: 'pt-br'}})
  }
}

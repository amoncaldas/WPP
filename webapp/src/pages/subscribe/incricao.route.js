import Subscribe from '@/pages/subscribe/Subscribe'
import store from '@/store/store'

export default {
  path: '/incricao',
  name: 'Assinar newsletter',
  component: Subscribe,
  beforeEnter: (to, from, next) => {
    store.commit('locale', 'pt-br')
    next({path: '/subscription', query: {l: 'pt-br'}})
  }
}

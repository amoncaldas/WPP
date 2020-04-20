import Subscribe from '@/pages/subscribe/Subscribe'
import store from '@/store/store'

export default {
  path: '/inscricao',
  name: 'Assinar newsletter',
  component: Subscribe,
  beforeEnter: (to, from, next) => {
    store.commit('locale', 'pt-br')
    next({path: '/subscribe', query: {l: 'pt-br'}})
  }
}

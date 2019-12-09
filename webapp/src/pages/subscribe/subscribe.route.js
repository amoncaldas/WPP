import Subscribe from '@/pages/subscribe/Subscribe'
import store from '@/store/store'

export default {
  path: '/subscribe',
  name: 'Subscribe',
  component: Subscribe,
  beforeEnter: (to, from, next) => {
    store.commit('locale', 'en-us')
    next()
  }
}
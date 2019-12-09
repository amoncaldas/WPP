import Unsubscribe from '@/pages/unsubscribe/Unsubscribe'
import store from '@/store/store'

export default {
  path: '/unsubscribe/:key/:email',
  name: 'Unsubscribe',
  component: Unsubscribe,
  beforeEnter: (to, from, next) => {
    store.commit('locale', 'en-us')
    next()
  }
}

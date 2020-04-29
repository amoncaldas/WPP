import Unsubscribe from '@/pages/unsubscribe/Unsubscribe'

export default {
  path: '/unsubscribe/:code',
  name: 'Unsubscribe',
  component: Unsubscribe,
  beforeEnter: (to, from, next) => {
    next()
  }
}

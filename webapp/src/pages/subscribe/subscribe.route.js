import Subscribe from '@/pages/subscribe/Subscribe'

export default {
  path: '/subscribe',
  name: 'Subscribe',
  component: Subscribe,
  beforeEnter: (to, from, next) => {
    next()
  }
}

import Subscribe from '@/pages/subscribe/Subscribe'

export default {
  path: '/incricao',
  name: 'Assinar newsletter',
  component: Subscribe,
  beforeEnter: (to, from, next) => {
    next({path: '/subscription', query: {l: 'pt-br'}})
  }
}

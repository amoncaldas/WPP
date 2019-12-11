import Contact from '@/pages/contact/Contact'

export default {
  path: '/contato',
  component: Contact,
  beforeEnter: (to, from, next) => {
    next({path: '/contact', query: {l: 'pt-br'}})
  }
}

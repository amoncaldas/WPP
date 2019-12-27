import Register from '@/pages/register/Register'

export default {
  path: '/register',
  name: 'Register',
  component: Register,
  beforeEnter: (to, from, next) => {
    next()
  }
}

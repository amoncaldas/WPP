import Register from '@/pages/register/Register'

export default {
  path: '/cadastro',
  name: 'Registro',
  component: Register,
  beforeEnter: (to, from, next) => {
    next({path: '/register', query: {l: 'pt-br'}})
  }
}

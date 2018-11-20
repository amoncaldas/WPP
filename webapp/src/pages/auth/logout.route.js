import store from '@/store/store'

export default {
  path: '/logout',
  name: 'Logout',
  beforeEnter: (to, from, next) => {
    store.dispatch('logout').then(() => {
      localStorage.clear()
      next('/login')
    })
  }
}

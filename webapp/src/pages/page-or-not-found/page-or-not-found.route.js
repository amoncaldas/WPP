import PageOrNotFound from './PageOrNotFound'
import Section from '@/support/section'
import store from '@/store/store'

export default {
  path: '*',
  name: 'PageOrNotFound',
  component: PageOrNotFound,
  beforeEnter: (to, from, next) => {
    let currentSection = Section.getCurrentSection()
    store.commit('currentSection', currentSection)
    store.commit('postTypeEndpoint', 'pages')
    next()
  }
}

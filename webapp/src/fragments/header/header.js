import menuManager from '@/support/menu-manager'
import LocaleChanger from '@/fragments/locale/Locale'
import utils from '@/support/utils'
import wpp from '@/support/wpp'

export default {
  data () {
    return {
      drawer: true,
      clipped: false,
      menuData: [],
      h1: ''
    }
  },
  methods: {
    toggleSidebar () {
      this.$store.commit('setLeftSideBarIsOpen', !this.$store.getters.leftSideBarOpen)
    },
    loadData () {
      let context = this
      this.$store.dispatch('fetchMainMenu').then(() => {
        context.menuData = context.$store.getters.mainMenu
      })
    }
  },
  computed: {
    logoUrl () {
      return wpp.logoUrl()
    },
    appTitle () {
      let title = this.$store.getters.options.site_title.trim()
      return title
    },
    menuItems () {
      return this.menuData
    },
    searchUrl () {
      let searchUrl = '/'
      let queryParams = utils.getUrlParams()
      if (queryParams.l) {
        searchUrl = `?l=${queryParams.l}`
      }
      if (searchUrl.indexOf('?') > -1) {
        searchUrl += '&'
      } else {
        searchUrl += '?'
      }
      searchUrl += 's='
      return searchUrl
    }
  },
  components: {
    LocaleChanger
  },
  created () {
    this.loadData()

    this.eventBus.$on('localeChanged', () => {
      this.loadData()
    })
    this.eventBus.$on('routeChanged', (routeParams) => {
      if (this.menuItems.length > 0) {
        menuManager.setMenuActiveStatus(this.menuItems, routeParams.to)
      }
    })

    this.h1 = this.$store.getters.options.site_title

    this.eventBus.$on('titleChanged', (title) => {
      this.h1 = title
    })
  }
}

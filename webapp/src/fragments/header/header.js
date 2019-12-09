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
    },
    routeToSearch () {
      this.$router.push({path: '/', query: { l: this.$store.getters.locale, s: '' }})
    },
    goHome () {
      this.$router.push({path: '/', query: { l: this.$store.getters.locale }})
    }
  },
  computed: {
    logoUrl () {
      return wpp.logoUrl()
    },
    appTitle () {
      let title = `${this.$store.getters.options.site_title.trim()} | ${this.$store.getters.options.short_name}`
      return title
    },
    menuItems () {
      return this.menuData
    },
    searchUrl () {
      let query = this.searchQuery
      let searchUrl = `/${query}`
      return searchUrl
    },
    searchQuery () {
      let searchQuery = ''
      let queryParams = utils.getUrlParams()
      if (queryParams.l) {
        searchQuery = `?l=${queryParams.l}`
      }
      if (searchQuery.indexOf('?') > -1) {
        searchQuery += '&'
      } else {
        searchQuery += '?'
      }
      searchQuery += 's='
      return searchQuery
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

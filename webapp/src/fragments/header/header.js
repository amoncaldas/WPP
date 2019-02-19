import menuManager from '@/support/menu-manager'
import LocaleChanger from '@/fragments/locale/Locale'

export default {
  data () {
    return {
      drawer: true,
      clipped: false,
      menuData: []
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
  computed : {
    logoUrl ()  {
      let url = this.$store.getters.options.site_relative_logo_url.trim()
      return url
    },
    appTitle ()  {
      let title = this.$store.getters.options.site_title.trim()
      return title
    },
    menuItems () {
      return this.menuData
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
  }
}

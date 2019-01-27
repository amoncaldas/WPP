import menuManager from '@/support/menu-manager'
import LocaleChanger from '@/fragments/locale/Locale'

export default {
  data () {
    return {
      drawer: true,
      clipped: false,
      menuItems: []
    }
  },
  methods: {
    toggleSidebar () {
      this.$store.commit('setLeftSideBarIsOpen', !this.$store.getters.leftSideBarOpen)
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
    }
  },
  components: {
    LocaleChanger
  },
  created () {
    this.$store.dispatch('fetchMainMenu').then(() => {
      this.menuItems = this.$store.getters.mainMenu
    })

    this.eventBus.$on('routeChanged', (routeParams) => {
      if (this.menuItems.length > 0) {
        menuManager.setMenuActiveStatus(this.menuItems, routeParams.to)
      }
    })
  }
}

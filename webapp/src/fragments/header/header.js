import menuManager from '@/support/menu-manager'

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

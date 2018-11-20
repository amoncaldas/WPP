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
    this.$store.dispatch('fetchHeadMenu').then(() => {
      this.menuItems = this.$store.getters.headMenu
    })

    this.eventBus.$on('routeChanged', (routeParams) => {
      if (this.menuItems.length > 0) {
        menuManager.setMenuActiveStatus(this.menuItems, routeParams.to)
      }
    })
  }
}

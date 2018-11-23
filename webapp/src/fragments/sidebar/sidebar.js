export default {
  data () {
    return {
      clipped: false,
      drawer: true,
      right: false,
      fixed: false,
      menuItems: [],
      subMenuOpen: []
    }
  },
  computed: {
    isSideBarOpen: {
      get () {
        return this.$store.getters.leftSideBarOpen
      },
      set (newValue) {
        this.$store.commit('setLeftSideBarIsOpen', newValue)
      }
    }
  },
  created () {
    this.$store.dispatch('fetchMainMenu').then(() => {
      this.menuItems = this.$store.getters.mainMenu
    })
  }
}

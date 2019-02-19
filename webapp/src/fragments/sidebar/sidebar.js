export default {
  data () {
    return {
      clipped: false,
      drawer: true,
      right: false,
      fixed: false,
      menuData: [],
      subMenuOpen: []
    }
  },
  methods: {
    loadData () {
      let context = this
      this.$store.dispatch('fetchMainMenu').then(() => {
        context.menuData = context.$store.getters.mainMenu
      })
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
    },
    menuItems () {
      return this.menuData
    }
  },
  created () {
    this.loadData()

    this.eventBus.$on('localeChanged', () => {
      this.loadData()
    })
  }
}

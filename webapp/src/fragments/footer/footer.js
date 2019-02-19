
export default {
  data () {
    return {
      footerMainSiteName: null,
      menuData: [],
      drawer: true,
      clipped: false,
    }
  },
  computed: {
    currentYear () {
      return (new Date()).getFullYear()
    },
    menuItems () {
      return this.menuData
    },
    developedByLink () {
      return 'http://codigocriativo.com'
    },
    height () {
      return this.$vuetify.breakpoint.smAndDown ? ((this.menuItems.length * 10) + 100) : 100
    }
  },
  methods: {
    loadData () {
      let context = this
      this.$store.dispatch('fetchSecondaryMenu').then(() => {
        context.menuData = context.$store.getters.secondaryMenu
      })
    }
  },
  created () {

    this.loadData()
    this.footerMainSiteName = this.$store.getters.options.site_title

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

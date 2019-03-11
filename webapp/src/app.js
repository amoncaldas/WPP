import Header from '@/fragments/header/Header'
import Sidebar from '@/fragments/sidebar/Sidebar'
import Footer from '@/fragments/footer/Footer'
import Toaster from '@/fragments/toaster/Toaster'
import Welcome from '@/fragments/welcome/Welcome'
import Confirm from '@/fragments/dialogs/confirm/Confirm'
import Info from '@/fragments/dialogs/info/Info'

export default {
  data () {
    return {
      items: [{
        icon: 'bubble_chart',
        title: 'Inspire'
      }],
      miniVariant: false,
      fixed: false,
      title: 'WPP',
      showLoading: false
    }
  },
  name: 'App',
  components: {
    appHeader: Header,
    appSidebar: Sidebar,
    appFooter: Footer,
    appToaster: Toaster,
    appWelcome: Welcome,
    appConfirm: Confirm,
    appInfo: Info
  },
  watch: {
    '$store.getters.currentSection': {
      handler: function () {
        this.setAppearance()
      },
      deep: true
    }
  },
  methods: {
    setAppearance () {
      let extra = this.$store.getters.currentSection.extra
        let customBackground = null
        if (extra.set_custom_appearance) {
          if (extra.bg_image) {
            customBackground = extra.bg_image
          } else if (extra.bg_color) {
            customBackground = extra.bg_color
          }
        }

        if(customBackground) {
          document.getElementById('app').style.background = `url(${customBackground}) no-repeat center top`
        } else {
          document.getElementById('app').style.background = this.$store.getters.defaultBackground
        }

        // modify the modifiable colors
        this.$vuetify.theme.primary = this.theme.primary = extra.theme_primary || this.$store.getters.defaultTheme.primary
        this.$vuetify.theme.secondary = this.theme.secondary = extra.theme_secondary || this.$store.getters.defaultTheme.secondary
        this.$vuetify.theme.accent = this.theme.accent = extra.theme_accent || this.$store.getters.defaultTheme.accent
        this.$vuetify.theme.dark = this.theme.dark = extra.theme_dark || this.$store.getters.defaultTheme.dark

        if (extra.is_dark !== undefined) {
          this.$store.commit('isDark', extra.is_dark)
        }
        this.$forceUpdate()
    },
    backupDefaultAppearance () {
      if (!this.$store.getters.defaultBackground) {
        this.$store.commit('defaultBackground', document.body.style.background)
      }
      if (!this.$store.getters.isDark) {
        this.$store.commit('isDark', true)
      }
      if (!this.$store.getters.defaultTheme) {
        let defaultTheme = Object.assign({}, this.theme)
        this.$store.commit('defaultTheme', defaultTheme)
      }
    }
  },
  created () {
    this.eventBus.$on('showLoading', (value) => {
      this.showLoading = value
    })
    this.eventBus.$on('titleChanged', (title) => {
      this.title = title
    })
    this.backupDefaultAppearance()
  }
}

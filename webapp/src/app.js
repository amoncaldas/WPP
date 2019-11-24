import Header from '@/fragments/header/Header'
import Sidebar from '@/fragments/sidebar/Sidebar'
import Footer from '@/fragments/footer/Footer'
import Toaster from '@/fragments/toaster/Toaster'
import Confirm from '@/fragments/dialogs/confirm/Confirm'
import Info from '@/fragments/dialogs/info/Info'

export default {
  data () {
    return {
      title: null,
      lang: null,
      showLoading: false
    }
  },
  name: 'App',
  components: {
    appHeader: Header,
    appSidebar: Sidebar,
    appFooter: Footer,
    appToaster: Toaster,
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

      if (customBackground) {
        let bgRepeat = extra.bg_repeat || 'no-repeat'
        let bgPosition = extra.bg_position || 'center top'
        document.getElementById('app').style.background = `url(${customBackground}) ${bgRepeat} ${bgPosition}`
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
      } else {
        this.$store.commit('isDark', true)
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
    this.title = this.$store.getters.options.site_title
    this.eventBus.$on('showLoading', (value) => {
      this.showLoading = value
    })
    this.eventBus.$on('titleChanged', (title) => {
      title = title.indexOf(this.$store.getters.options.site_title) === -1 ? `${title} | ${this.$store.getters.options.site_title}` : title
      this.title = title
    })

    this.eventBus.$on('langChanged', (lang) => {
      this.lang = lang
    })
    this.lang = this.$store.getters.locale


    this.backupDefaultAppearance()
  }
}

import Header from '@/fragments/header/Header'
import Sidebar from '@/fragments/sidebar/Sidebar'
import Footer from '@/fragments/footer/Footer'
import Toaster from '@/fragments/toaster/Toaster'
import Confirm from '@/fragments/dialogs/confirm/Confirm'
import Info from '@/fragments/dialogs/info/Info'
import wpp from '@/support/wpp'
import VueScrollTo from 'vue-scrollto'
import Section from '@/support/section'

export default {
  data () {
    return {
      title: null,
      lang: null,
      showLoading: false,
      dataAndPrivacyPolicyAccepted: false
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
    },
    $route: {
      handler: function () {
        let currentSection = Section.getCurrentSection()
        this.$store.commit('currentSection', currentSection)
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
    },
    acceptDataAndPrivacyPolicy () {
      this.dataAndPrivacyPolicyAccepted = true
      localStorage.setItem('dataAndPrivacyPolicyAccepted', true)
    },
    getDataAndPrivacyUrl () {
      let url = this.$store.getters.options['data_and_privacy_url_' + this.$store.getters.locale]
      return this.buildLink(url)
    },
    setTitle (title) {
      title = this.$options.filters.capitalize(title)

      if (this.$route.meta.single || this.$route.meta.page) {
        let archiveTitle = wpp.getArchiveTranslated()
        title = `${title} | ${this.$options.filters.capitalize(archiveTitle)}`
      }
      if (this.$store.getters.currentSection && this.$store.getters.currentSection.path !== '/') {
        let sectionTitle = this.$store.getters.currentSection.title.rendered || this.$store.getters.currentSection.title
        title = `${title} | ${sectionTitle}`
      }
      this.title = `${title} | ${this.$store.getters.options.short_name}`
    }
  },
  computed: {
    showDataAndPrivacyPolicy () {
      this.dataAndPrivacyPolicyAccepted = localStorage.getItem('dataAndPrivacyPolicyAccepted')
      let url = this.getDataAndPrivacyUrl()
      let show = !this.dataAndPrivacyPolicyAccepted && url !== undefined && url !== null
      return show
    }
  },
  created () {
    this.title = this.$store.getters.options.site_title
    let context = this

    this.eventBus.$on('showLoading', (value) => {
      context.showLoading = value
    })
    this.eventBus.$on('titleChanged', (title) => {
      context.setTitle(title)
      VueScrollTo.scrollTo('body', 1000, {})
    })

    this.eventBus.$on('langChanged', (lang) => {
      this.lang = lang
    })
    this.lang = this.$store.getters.locale
    this.backupDefaultAppearance()
  }
}

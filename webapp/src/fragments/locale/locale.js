import utils from '@/support/utils'
import Section from '@/support/section'
import appConfig from '@/config'

export default {
  name: 'locale-changer',
  data () {
    return {
      locales: [],
      currentLocale: null
    }
  },
  created () {
    this.currentLocale = this.$i18n.locale

    let context = this
    this.eventBus.$on('setLocaleFromContentLocale', (locale) => {
      if (locale !== context.currentLocale) {
        context.$i18n.locale = context.currentLocale = locale
        context.$store.commit('locale', context.currentLocale)
      }
    })

    this.populateLocalesFromOptions()
    if (!this.$store.getters.locale) {
      this.setFromUrlOrStorage()
    }
  },
  watch: {
    '$store.getters.locale': function () {
      this.eventBus.$emit('localeChanged', this.currentLocale)
    }
  },
  methods: {
    selectChanged (newLocale) {
      this.$i18n.locale = this.currentLocale = newLocale
      this.$store.commit('locale', this.currentLocale)
      this.afterLocaleUpdate()
    },
    afterLocaleUpdate () {
      this.$i18n.locale = this.currentLocale
      // Store the new locale
      this.$store.commit('locale', this.currentLocale)

      // et the current home based on the locale
      let currentSection = Section.getCurrentSection()
      this.$store.commit('currentSection', currentSection)

      // Change the url
      this.$router.push({path: '/', query: { l: this.currentLocale }})
    },
    supportedLocales () {
      return Object.keys(this.$i18n.messages)
    },
    populateLocalesFromOptions () {
      let locales = this.$store.getters.options.locales
      let supportedLocales = this.supportedLocales()
      for (let key in locales) {
        if (locales[key].slug !== 'neutral' && supportedLocales.includes(locales[key])) {
          let title = locales[key].split('-')[0].toUpperCase()
          this.locales.push({title: title, value: locales[key]})
        }
      }
      if (this.locales.length === 1) {
        this.currentLocale = this.locales[0]
      }
    },
    setFromUrlOrStorage () {
      let queryParams = utils.getUrlParams()
      if (queryParams.l) {
        let urlLocale = queryParams.l
        let supportedLocales = this.supportedLocales()
        if (supportedLocales.includes(urlLocale) && urlLocale !== this.currentLocale) {
          this.currentLocale = this.$i18n.locale = urlLocale
          this.afterLocaleUpdate()
        } else if (!queryParams.l) {
          this.setFromStorage()
        }
      } else {
        this.setFromStorage()
      }
    },
    setFromStorage () {
      let context = this
      this.$store.dispatch('autoSetLocale').then((autoSetLocale) => {
        // Redirect to the correct locale url if
        // the autoLocale is different from the one in the html
        // and the url is '/'
        let defaultLocale = this.$store.getters.options.default_locale
        if (autoSetLocale !== defaultLocale && window.location.pathname === '/') {
          context.currentLocale = context.$i18n.locale = autoSetLocale
          context.afterLocaleUpdate()
        }
      })
    }
  }
}

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
    let locales = this.$store.getters.options.locales
    let supportedLocales = this.supportedLocales()
    for (let key in locales) {
      if (locales[key].slug !== 'neutral' && supportedLocales.includes(locales[key].slug)) {
        let title = locales[key].slug.split('-')[0].toUpperCase()
        this.locales.push({title: title, value: locales[key].slug})
      }
    }
    if (this.locales.length === 1) {
      this.currentLocale = this.locales[0]
      this.afterLocaleUpdate()
    }
    let context = this
    this.eventBus.$on('setLocaleFromContentLocale', (locale) => {
      context.currentLocale = locale
      context.$i18n.locale = this.currentLocale
      context.$store.commit('locale', this.currentLocale)
    })
  },
  watch: {
    /**
     * Every time the locale changes, we need to run afterLocaleUpdate
     * @param {*} to
     * @param {*} from
     */
    'currentLocale' (to, from) {
      if (this.$i18n.locale !== this.currentLocale) {
        this.afterLocaleUpdate()
      }
    }
  },
  methods: {
    afterLocaleUpdate () {
      this.$i18n.locale = this.currentLocale
      this.$store.commit('locale', this.currentLocale)

      // if not, go to home after change the locale
      // Once the home is reloaded, the content in the new language wil be listed
      setTimeout(() => {
        window.location.href = '/'
      }, 100)
    },
    supportedLocales () {
      return Object.keys(this.$i18n.messages)
    }
  }
}

import utils from '@/support/utils'

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
      context.currentLocale = locale
    })

    this.populateLocalesFromOptions()
    this.setFromUrl()

  },
  watch: {
    /**
     * Every time the locale changes, we need to run afterLocaleUpdate
     * @param {*} to
     * @param {*} from
     */
    'currentLocale' (to, from) {
      if (this.$i18n.locale !== this.currentLocale) {
        this.$i18n.locale = this.currentLocale
        this.$store.commit('locale', this.currentLocale)
        this.afterLocaleUpdate()
      }
    }
  },
  methods: {
    afterLocaleUpdate () {
      this.$i18n.locale = this.currentLocale
      // Store the new locale and reload going to home
      this.$store.commit('locale', this.currentLocale)
      this.eventBus.$emit('langChanged', this.currentLocale)
      window.location.href = '/'
    },
    supportedLocales () {
      return Object.keys(this.$i18n.messages)
    },
    populateLocalesFromOptions () {
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
    },
    setFromUrl () {
      let queryParams = utils.getUrlParams()
      if (queryParams['l']) {
        let urlLocale = queryParams['l']
        let supportedLocales = this.supportedLocales()
        if (supportedLocales.includes(urlLocale) && urlLocale != this.currentLocale) {
          this.currentLocale = this.$i18n.locale = urlLocale
          this.afterLocaleUpdate()
        }
      }
    }
  }
}

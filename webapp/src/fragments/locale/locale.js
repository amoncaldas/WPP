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
      if (locale !== context.currentLocale) {
        context.$i18n.locale = context.currentLocale = locale
        context.$store.commit('locale', context.currentLocale)
      }
    })

    this.populateLocalesFromOptions()
    this.setFromUrl()
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
      // Store the new locale and reload going to home
      this.$store.commit('locale', this.currentLocale)
      window.location.href = `/?l=${this.currentLocale}`
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

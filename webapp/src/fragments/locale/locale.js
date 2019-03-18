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
    if (this.locales.length == 1) {
      this.currentLocale = this.locales[0]
      this.afterLocaleUpdate()
    }
  },
  watch: {
    /**
     * Every time the route change, we have to run the scroll
     * to make sure the page content is focused/scrolled to the current route content
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
      this.$router.push({ name: 'Home' })
      this.eventBus.$emit('localeChanged', this.currentLocale)
    },
    supportedLocales() {
      return Object.keys(this.$i18n.messages);
    }
  }
}

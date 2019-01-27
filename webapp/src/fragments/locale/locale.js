export default {
  name: 'locale-changer',
  data () {
    return {
      locales: [{title: 'EN', value: 'en-us'}, {title: 'PT', value: 'pt-br'}],
      currentLocale: null
    }
  },
  created () {
    this.currentLocale = this.$i18n.locale
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
        this.$i18n.locale = this.currentLocale
        this.$store.commit('locale', this.currentLocale)
        this.eventBus.$emit('localeChanged', this.currentLocale)
      }
    }
  }
}

import Section from '@/support/section'
import Posts from '@/fragments/posts/Posts'

export default {
  data: () => ({
    postType: null,
    currentSection: null,
    title: null
  }),
  components: {
    Posts
  },
  created () {
    this.loadData()
    this.eventBus.$on('localeChanged', () => {
      this.loadData()
    })
  },
  methods: {
    loadData () {
      this.currentSection = Section.getCurrentHomeSection()
      let translation = this.getArchiveTranslated()
      this.postType = this.$store.getters.postTypeEndpoint
      this.title = translation.title
      this.eventBus.$emit('titleChanged', `${translation.title} | ${this.$store.getters.options.site_title}`)
    },
    getArchiveTranslated () {
      let translations = this.$store.getters.options.post_type_translations
      let context = this
      let translation = {endpoint: this.postType, title: this.postType}

      this.currentSection.acf.list_post_endpoints.forEach(endpoint => {
        let localesTranslation = context.lodash.find(translations, (locales) => {
          return context.lodash.find(locales, locale => {
            return locale.path === endpoint
          })
        })
        if (localesTranslation) {
          let matchTranslation = localesTranslation[context.$store.getters.locale]
          translation = {endpoint: matchTranslation.path, title: matchTranslation.title}
        }
      })
      return translation
    }
  }
}

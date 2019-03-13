import Posts from '@/fragments/posts/Posts'

export default {
  data: () => ({
    postType: null,
    currentSection: null,
    title: null,
    parentSectionId: null
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
      if (this.$store.getters.currentSection && this.$store.getters.currentSection.path !== '/') {
        this.parentSectionId = this.$store.getters.currentSection.id
      }
      let translation = this.getArchiveTranslated()
      this.postType = this.$store.getters.postTypeEndpoint
      this.title = translation
      this.eventBus.$emit('titleChanged', `${translation} | ${this.$store.getters.options.site_title}`)
    },
    getArchiveTranslated () {
      let translations = this.$store.getters.options.post_type_translations
      let context = this
      let translation = this.$store.getters.postTypeEndpoint
      let localesTranslation = context.lodash.find(translations, (locales) => {
        return context.lodash.find(locales, locale => {
          return locale.path === this.$store.getters.postTypeEndpoint
        })
      })
      if (localesTranslation) {
        let matchTranslation = localesTranslation[context.$store.getters.locale]
        translation = matchTranslation.title
      }
      return translation
    }
  }
}

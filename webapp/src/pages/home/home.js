import SectionsMap from '@/fragments/sections-map/SectionsMap'
import Posts from '@/fragments/posts/Posts'

export default {
  data: () => ({
    valid: false,
    activeTab: '0',
    listPostEndpoints: [],
    currentSection: null
  }),
  components: {
    SectionsMap,
    Posts
  },
  created () {
    if (this.$route.query.tab) {
      this.activeTab = this.$route.query.tab
    }

    let context = this
    this.currentSection = this.lodash.find(this.$store.getters.sections, (section) => {
      return section.link === context.$route.path && section.locale === context.$store.getters.locale
    })
    this.setListingPosts()
    // emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$store.getters.options.site_title)
  },
  methods: {
    setListingPosts () {
      if (Array.isArray(this.currentSection.acf.list_post_endpoints)) {
        let context = this
        let translations = context.$store.getters.options.post_type_translations

        this.currentSection.acf.list_post_endpoints.forEach(endpoint => {
          let localesTranslation = context.lodash.find(translations, (locales) => {
            return context.lodash.find(locales, locale => {
              return locale.url === endpoint
            })
          })
          if (localesTranslation) {
            let translation = localesTranslation[context.$store.getters.locale]
            context.listPostEndpoints.push({endpoint: endpoint, title: translation.title})
          }
        })
      }
    }
  }
}

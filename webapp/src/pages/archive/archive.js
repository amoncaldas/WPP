import Posts from '@/fragments/posts/Posts'
import Sections from '@/fragments/sections/Sections'
import wpp from '@/support/wpp'

export default {
  data: () => ({
    postType: null,
    currentSection: null,
    title: null,
    parentSectionId: null,
    page: 1
  }),
  components: {
    Posts,
    Sections
  },
  created () {
    this.loadData()
    this.eventBus.$on('localeChanged', () => {
      this.loadData()
    })
    if (this.$route.query.page) {
      this.page = Number(this.$route.query.page)
    }
  },
  methods: {
    loadData () {
      if (this.$store.getters.currentSection && this.$store.getters.currentSection.path !== '/') {
        this.parentSectionId = this.$store.getters.currentSection.id
      }
      let translation = wpp.getArchiveTranslated()
      this.postType = this.$store.getters.postTypeEndpoint
      this.title = translation
      this.eventBus.$emit('titleChanged', translation)
    }
  }
}

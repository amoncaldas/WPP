import Posts from '@/fragments/posts/Posts'
import Sections from '@/fragments/sections/Sections'
import wpp from '@/support/wpp'

export default {
  data: () => ({
    postType: null,
    loaded: false,
    currentSection: null,
    title: null,
    parentSectionId: null,
    page: 1
  }),
  components: {
    Posts,
    Sections
  },
  watch: {
    $route: {
      handler: function () {
        this.loadData()
      },
      deep: true
    }
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
      this.loaded = false
      this.postType = null
      let context = this
      setTimeout(() => {
        if (context.$store.getters.currentSection && context.$store.getters.currentSection.path !== '/') {
          context.parentSectionId = context.$store.getters.currentSection.id
        }
        let translation = wpp.getArchiveTranslated()
        context.postType = context.$store.getters.postTypeEndpoint
        context.title = translation
        context.eventBus.$emit('titleChanged', translation)
        context.loaded = true
      }, 100)
    }
  }
}

import Posts from '@/fragments/posts/Posts'
import Sections from '@/fragments/sections/Sections'
import wpp from '@/support/wpp'
import Section from '@/support/section'

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
        let currentSection = Section.getCurrentSection()
        this.$store.commit('currentSection', currentSection)

        if (currentSection && currentSection.path !== '/') {
          context.parentSectionId = currentSection.id
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

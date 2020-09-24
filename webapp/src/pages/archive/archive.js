import Posts from '@/fragments/posts/Posts'
import Sections from '@/fragments/sections/Sections'
import wpp from '@/support/wpp'
import Section from '@/support/section'
import Search from '@/fragments/forms/search/Search'

export default {
  data: () => ({
    postTypeEndpoint: null,
    loaded: false,
    currentSection: null,
    title: null,
    parentSectionId: null,
    page: 1,
    order: 'asc'
  }),
  components: {
    Posts,
    Sections,
    Search
  },
  watch: {
    $route: {
      handler: function () {
        this.loadData()
      },
      deep: true
    }
  },
  computed: {
    postType () {
      let postType = wpp.getPostTypeFromEndpoint(this.$store.getters.postTypeEndpoint)
      return postType
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
      this.postTypeEndpoint = null
      let context = this
      setTimeout(() => {
        let currentSection = Section.getCurrentSection()
        this.$store.commit('currentSection', currentSection)

        if (currentSection && currentSection.path !== '/') {
          context.parentSectionId = currentSection.id
        }
        let translation = wpp.getArchiveTranslated()
        context.postTypeEndpoint = context.$store.getters.postTypeEndpoint
        context.title = translation
        context.eventBus.$emit('titleChanged', translation)
        context.loaded = true
      }, 100)
    },
    orderChanged (order) {
      this.order = order
      this.syncUrl()
    },
    pageChanged (page) {
      this.page = page
      this.syncUrl()
    },
    syncUrl () {
      let query = {order: this.order, page: this.page}
      this.$router.push({path: location.pathname, query: query})
    }
  }
}

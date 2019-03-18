import Post from '@/fragments/post/Post'
import Posts from '@/fragments/posts/Posts'
import Sections from '@/fragments/sections/Sections'
import NotFoundComponent from '@/fragments/not-found/NotFound'
import postService from '@/shared-services/post-service'
import postSupport from '@/support/post'


export default {
  components: {
    Post,
    Posts,
    Sections,
    NotFoundComponent
  },
  data () {
    return {
      notFound: false,
      post: null,
      loaded: false,
      currentSection: null
    }
  },
  computed: {
    hasSidebar () {
      return this.loaded && this.post && this.post.extra.show_sidebar
    },
    sidebarPostTypes () {
      let types = postSupport.getListingPosts(this.post)
      return types
    },
    parentSectionId () {
      if (this.currentSection && this.currentSection.id) {
        return this.currentSection.id
      }
    },
    maxInSidebar () {
      let max = this.post.extra.max_in_side_bar !== undefined ? this.post.extra.max_in_side_bar : 3
      return Number(max)
    }
  },
  created () {
    this.currentSection = this.$store.getters.currentSection
    let pageTypes = this.$store.getters.options.page_like_types
    pageTypes = Array.isArray(pageTypes) ? pageTypes : [pageTypes]

    if (pageTypes.includes(this.$store.getters.postTypeEndpoint)) {
      this.renderAsPage = true
    }
    this.loadData()
  },
  watch: {
    $route: function () {
      this.post = null
      setTimeout(() => {
        this.loadData()
      }, 100)
    }
  },
  methods: {
    loadData () {
      let context = this
      let endpoint = this.$store.getters.postTypeEndpoint
      let endpointAppend = null
      endpointAppend = `${endpoint}/${this.$route.params.postId}?_embed=1`
      postService.get(endpointAppend).then((post) => {
        context.post = post
        context.loaded = true
        let postTitle = context.post.title.rendered || context.post.title
        context.eventBus.$emit('titleChanged', `${postTitle} | ${context.$store.getters.options.site_title}`)
      }).catch(error => {
        console.log(error)
        context.loaded = true
        context.showError(this.$t('post.thePostCouldNotBeLoaded'))
        context.notFound = true
      })
    },
  },
}

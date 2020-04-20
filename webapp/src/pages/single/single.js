import Post from '@/fragments/post/Post'
import Posts from '@/fragments/posts/Posts'
import Sections from '@/fragments/sections/Sections'
import NotFoundComponent from '@/fragments/not-found/NotFound'
import postService from '@/shared-services/post-service'
import postSupport from '@/support/post'
import wpp from '@/support/wpp'

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
    this.ready = true
  },
  watch: {
    $route: {
      handler: function () {
        this.loaded = false
        this.post = null
        setTimeout(() => {
          this.loadData()
        }, 100)
      },
      deep: true
    }
  },
  methods: {
    loadData () {
      let context = this
      let endpoint = this.$store.getters.postTypeEndpoint
      let endpointAppend = null
      endpointAppend = `${endpoint}/${this.$route.params.postId}?_embed`

      // Get the post data
      postService.get(endpointAppend).then((post) => {
        // build the short post path for comparasion
        let postShortPath = `/${wpp.getCurrentEndpointTranslated()}/${post.id}`

        // Check if the current url matchs the post path
        // or the post short url to avoid loading a single
        // by the id but with wrong title, type or section path
        if (post.path === context.$route.fullPath || context.$route.fullPath === postShortPath) {
          context.post = post
          let postTitle = context.post.title.rendered || context.post.title
          context.eventBus.$emit('titleChanged', postTitle)
          context.eventBus.$emit('setLocaleFromContentLocale', context.post.locale)
        } else {
          context.notFound = true
        }
        context.loaded = true
      }).catch(error => {
        console.log(error)
        context.loaded = context.notFound = true
        context.showError(context.$t('post.thePostCouldNotBeLoaded'))
      })
    }
  }
}

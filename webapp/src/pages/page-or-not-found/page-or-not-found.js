import Post from '@/fragments/post/Post'
import Sections from '@/fragments/sections/Sections'
import NotFoundComponent from '@/fragments/not-found/NotFound'
import postService from '@/shared-services/post-service'
import Subscribe from '@/fragments/subscribe/Subscribe'

export default {
  components: {
    Post,
    Sections,
    NotFoundComponent,
    Subscribe
  },
  data () {
    return {
      notFound: false,
      post: null,
      loaded: false
    }
  },
  created () {
    this.loadData()
  },
  computed: {
    showNewsletterForm () {
      let show = true
      if (this.loaded && this.post && this.post.extra.hide_newsletter_sidebar === true) {
        show = false
      }
      return show
    },
    hasSidebar () {
      return this.loaded && this.post && this.post.extra.show_sidebar
    }
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
      let uriParts = location.href.trim('/').split('/')
      let postName = null
      if (uriParts.length > 0 && uriParts[uriParts.length - 1].length > 0) {
        postName = uriParts[uriParts.length - 1]
      }

      if (!postName) {
        this.setNotFound()
      } else {
        let context = this
        let endpointAppend = `pages?slug=${postName}&_embed`

        // Get page data
        postService.get(endpointAppend).then((data) => {
          context.post = context.extractPostFromResponseData(data)

          if (!context.post || context.post.path !== context.$route.fullPath) {
            context.setNotFound()
          } else {
            // If in single mode, set the site title
            let pageTitle = context.post.title.rendered || context.post.title
            context.eventBus.$emit('titleChanged', pageTitle)
            context.eventBus.$emit('setLocaleFromContentLocale', context.post.locale)
            context.loaded = true
          }
        }).catch(error => {
          context.setNotFound(error)
        })
      }
    },
    /**
     * Set the component in a not found state
     * @param {*} error
     */
    setNotFound (error = null) {
      this.loaded = true
      this.notFound = true
      this.eventBus.$emit('titleChanged', this.$t('pageOrNotFound.notFound'))
      if (error) {
        console.log(error)
      }
    },
    /**
     * Extract the post from a response data
     * @param {*} data
     */
    extractPostFromResponseData (data) {
      let post = null
      if (Array.isArray(data)) {
        post = data.length > 0 ? data[0] : null
      } else {
        post = data
      }
      return post
    }
  }
}

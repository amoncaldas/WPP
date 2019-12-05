import Post from '@/fragments/post/Post'
import Sections from '@/fragments/sections/Sections'
import NotFoundComponent from '@/fragments/not-found/NotFound'
import postService from '@/shared-services/post-service'

export default {
  components: {
    Post,
    Sections,
    NotFoundComponent
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
      let uriParts = location.href.trim('/').split('/')
      let postName = ''
      if (uriParts.length > 0) {
        postName = uriParts[uriParts.length - 1]
      }
      let endpointAppend = `pages?slug=${postName}&_embed`
      postService.get(endpointAppend).then((post) => {
        if (Array.isArray(post)) {
          if (post.length === 0) {
            context.notFound = true
            this.eventBus.$emit('titleChanged', this.$t('pageOrNotFound.notFound'))
          } else {
            context.post = post[0]
          }
        } else {
          context.post = post
        }
        if (context.post) {
          // If in single mode, set the site title
          let pageTitle = this.post.title.rendered || context.post.title
          context.eventBus.$emit('titleChanged', pageTitle)
          context.eventBus.$emit('setLocaleFromContentLocale', context.post.locale)
        }
        context.loaded = true
      }).catch(error => {
        console.log(error)
        context.loaded = true
        context.notFound = true
        // Set the not found page title
        this.eventBus.$emit('titleChanged', this.$t('pageOrNotFound.notFound'))
      })
    }
  }
}

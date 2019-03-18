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
  methods: {
    loadData () {
      let context = this
      let endpoint = this.$store.getters.postTypeEndpoint
      endpointAppend = `${endpoint}?slug=${this.$route.params.postName}&_embed=1`
      postService.get(endpointAppend).then((post) => {
        if (Array.isArray(post)) {
          if (post.length == 0) {
            this.$emit('notFound')
          } else {
            context.post = post[0]
          }
        } else {
          context.post = post
        }
        // If in single mdoe, set the site title
        let pageTitle = this.post.title.rendered || context.post.title
        context.eventBus.$emit('titleChanged', `${pageTitle} | ${context.$store.getters.options.site_title}`)
        context.loaded = true
      }).catch(error => {
        console.log(error)
        context.loaded = true
        context.showError(this.$t('post.thePostCouldNotBeLoaded'))
      })
    }
  },
}

import postService from '@/shared-services/post-service'
import Media from '@/fragments/media/Media'
import PostMap from '@/fragments/post-map/PostMap'

export default {
  name: 'post',
  created () {
    this.loadData()
  },
  watch: {
    $route: function () {
      this.post = null
      setTimeout(() => {
        this.loadData()
      },100)
    }
  },
  props: {
    postId: {
      required: false
    },
    postName: {
      required: false
    },
    postData: {
      required: false
    },
    noTopBorder: {
      default: false
    },
    mode: {
      type: String,
      default: 'list'
    },
    explicitLocale: {
      type: Boolean,
      default: false
    },

  },
  data () {
    return {
      post: null
    }
  },
  computed: {
    featuredMedia () {
      if (this.post._embedded && this.post._embedded['wp:featuredmedia']) {
        let media = this.post._embedded['wp:featuredmedia'][0]
        return media
      }
    },
    related () {
      if (this.post && this.post.data && this.post.data.related && Array.isArray(this.post.data.related)) {
        return this.post.data.related
      }
      return []
    },
    excerpt () {
      let content = this.content || ''
      return content.replace(/<(?:.|\n)*?>/gm, '').substring(0, 300)
    },
    content () {
      let content = ''
      if (this.post.content) {
        content =  this.post.content.rendered
      }
      if (this.post.data && this.post.data.content) {
        content =  this.post.data.content
      }
      return content
    },
  },
  methods: {
    loadData () {
      if (this.postData) {
        this.post = this.postData
      } else {
        let context = this
        let endpoint = this.$store.getters.postTypeEndpoint
        let endpointAppend = null
        if (this.postId) {
          endpointAppend = `${endpoint}/${this.postId}`
        } else if (this.postName) {
          endpointAppend = `${endpoint}?name=${this.postName}`
        }
        postService.get(endpointAppend).then((post) => {
          context.post = post
        }).catch(error => {
          console.log(error)
          context.showError(this.$t('post.thePostCouldNotBeLoaded'))
        })
      }
    },
  },
  components: {
    Media,
    PostMap
  },
  beforeCreate: function () {
    this.$options.components.Posts = require('@/fragments/posts/Posts.vue').default
  }
}

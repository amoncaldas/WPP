import postService from '@/shared-services/post-service'
import Media from '@/fragments/media/Media'


export default {
  created () {
    if (this.postId) {
      let context = this
      let endpoint = this.$store.getters.postTypeEndpoint
      let endpointAppend = `${endpoint}/${this.postId}`
      postService.get(endpointAppend).then((post) => {
        context.post = post
      }).catch(error => {
        console.log(error)
        context.showError(this.$t('post.thePostCouldNotBeLoaded'))
      })
    } else {
      this.post = this.postData
    }
  },
  props: {
    postId: {
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
    }
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
    }
  },
  methods: {
    excerpt() {
      return this.post.content.rendered.replace(/<(?:.|\n)*?>/gm, '').substring(0, 300)
    },
    goToSingle() {
      this.$router.push(this.postData.link)
    }
  },
  components: {
    Media
  },
}

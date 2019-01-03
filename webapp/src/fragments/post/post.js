import postService from '@/shared-services/post-service'
import Media from '@/fragments/media/Media'


export default {
  created () {
    if (this.postId) {
      let context = this
      postService.get(this.postId).then((post) => {
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
    }
  },
  data () {
    return {
      post: null
    }
  },
  computed: {

  },
  methods: {
    excerpt() {
      return this.post.content.rendered.replace(/<(?:.|\n)*?>/gm, '').substring(0, 300)
    }
  },
  components: {
    Media
  },
}

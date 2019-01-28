import postService from '@/shared-services/post-service'
import Post from '@/fragments/post/Post'

export default {
  created () {
    // extend this component, adding CRUD functionalities and load the tokens

    let context = this
    this.boxTitle = this.title || this.$t('posts.title')

    // get the data related to the userId defined
    let endpointAppend = `/${this.endpoint}?_embed`

    postService.query({}, endpointAppend).then((posts) => {
      context.posts = posts
    }).catch(error => {
      console.log(error)
      context.showError(this.$t('post.thePostCouldNotBeLoaded'))
    })
  },
  props: {
    endpoint: {
      default: 'post'
    },
    title: {
      required: false
    },
    columnsPerPost: {
      type: Number,
      default: 12
    }
  },
  data () {
    return {
      posts: [],
      boxTitle: null
    }
  },
  methods: {
    excerpt () {
      return this.post.content.replace(/<(?:.|\n)*?>/gm, '').substring(0, 300)
    }
  },
  components: {
    Post
  }
}

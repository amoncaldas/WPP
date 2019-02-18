import postService from '@/shared-services/post-service'
import Post from '@/fragments/post/Post'

export default {
  created () {
    // extend this component, adding CRUD functionalities and load the tokens

    this.boxTitle = this.title || this.$t('posts.title')
    this.loadPosts()
    this.eventBus.$on('localeChanged', () => {
      this.loadPosts()
    })
  },
  props: {
    endpoint: {
      default: 'posts'
    },
    title: {
      required: false
    },
    columnsPerPost: {
      type: Number,
      default: 12
    },
    exclude: {
      type: Array,
      default: function () { return [] }
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
    },
    loadPosts () {
      let endpointAppend = `/${this.endpoint}?_embed=1`
      if (this.exclude.length > 0) {
        let exclude = this.exclude.join(',')
        endpointAppend += `&exclude=${exclude}`
      }

      postService.query({}, endpointAppend).then((posts) => {
        this.posts = posts
      }).catch(error => {
        console.log(error)
        this.showError(this.$t('post.thePostCouldNotBeLoaded'))
      })
    }
  },
  components: {
    Post
  }
}

import postService from '@/shared-services/post-service'
import Post from '@/fragments/post/Post'

export default {
  name: 'posts',
  created () {
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
    },
    include: {
      type: Array,
      default: function () { return [] }
    }
  },
  data () {
    return {
      posts: []
    }
  },
  computed: {
    boxTitle () {
      return this.title || this.$t('posts.title')
    }
  },
  methods: {
    loadPosts () {
      let endpointAppend = `/${this.endpoint}?_embed=1`
      if (this.exclude.length > 0) {
        let exclude = this.exclude.join(',')
        endpointAppend += `&exclude=${exclude}`
      }
      if (this.include.length > 0) {
        let include = this.include.join(',')
        endpointAppend += `&include=${include}`
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

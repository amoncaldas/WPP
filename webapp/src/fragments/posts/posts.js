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
    },
    max: {
      type: Number,
      default: 10
    },
    page: {
      type: Number,
      default: 1
    },
    offset: {
      type: Number,
      default: 0
    },
    embed: {
      type: Boolean,
      default: true
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
      let filters = {
        page: this.page,
        per_page: this.max,
        offset: this.offset
      }

      if (this.embed) {
        filters._embed = 1
      }

      if (this.exclude.length > 0) {
        filters.exclude = this.exclude.join(',')
      }
      if (this.include.length > 0) {
        filters.include = this.include.join(',')
      }

      postService.query(filters, `/${this.endpoint}`).then((posts) => {
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

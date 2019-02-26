import postService from '@/shared-services/post-service'
import Post from '@/fragments/post/Post'

export default {
  name: 'posts',
  created () {
    this.currentPage = this.page
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
    },
    pagination: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {
      posts: [],
      total: null,
      totalPages: null,
      currentPage: null
    }
  },
  watch: {
    currentPage: function () {
      this.loadPosts()
    }
  },
  computed: {
    boxTitle () {
      return this.title || this.$t('posts.title')
    }
  },
  methods: {
    loadPosts () {
      // @see http://v2.wp-api.org/reference/posts/
      let filters = {
        page: this.currentPage,
        per_page: this.max,
      }

      // Offset value in the query is causing
      // a return of always the first page ?
      if (this.offset > 0) {
        filters.offset = this.offset
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

      postService.query(filters, `/${this.endpoint}`).then((response) => {
        this.posts = response
        if (response.raw && response.data) {
          this.posts = response.data
          this.total = Number(response.headers['x-wp-total'])
          this.totalPages = Number(response.headers['x-wp-totalpages'])
        }
      }).catch(error => {
        console.log(error)
        this.showError(this.$t('posts.thePostListCouldNotBeLoaded'))
      })
    }
  },
  components: {
    Post
  }
}

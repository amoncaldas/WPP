import postService from '@/shared-services/post-service'
import Post from '@/fragments/post/Post'
import VueScrollTo from 'vue-scrollto'

export default {
  name: 'posts',
  created () {
    this.firstLoad = true
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
    parentId: {
      type: Number,
      required: false
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
      default: 6
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
    },
    mode: {
      type: String,
      default: 'block'
    }
  },
  data () {
    return {
      posts: [],
      total: null,
      loaded: false,
      totalPages: null,
      firstLoad: true,
      currentPage: 1
    }
  },
  watch: {
    currentPage: function () {
      this.loadPosts()
    },
    endpoint: function () {
      this.loadPosts()
    },
    parentId: function () {
      this.loadPosts()
    }
  },
  computed: {
    boxTitle () {
      return this.title || this.$t('posts.title')
    },
    categories () {
      if (this.$route.query.cats) {
        return this.$route.query.cats.indexOf(',') > -1 ? this.$route.query.cats.split('') : [this.$route.query.cats]
      }
      return []
    },
    tags () {
      if (this.$route.query.p_tags) {
        return this.$route.query.p_tags.indexOf(',') > -1 ? this.$route.query.p_tags.split('') : [this.$route.query.p_tags]
      }
      return []
    },
    archiveLink () {
      if (this.$store.getters.currentSection.path !== '/') {
        return `${this.$store.getters.currentSection.path}/${this.endpoint}`
      } else {
        return `/${this.endpoint}`
      }
    }
  },
  methods: {
    loadPosts () {
      this.loaded = false
      // @see http://v2.wp-api.org/reference/posts/
      let filters = {
        page: this.currentPage,
        per_page: this.max
      }

      // Offset value in the query is causing
      // a return of always the first page ?
      if (this.offset > 0) {
        filters.offset = this.offset
      }

      if (this.parentId > 0) {
        filters.parent_id = this.parentId
      }

      if (this.embed) {
        filters._embed = 1
      }

      if (this.$route.query.cats) {
        filters.cats = this.$route.query.cats
      }

      if (this.$route.query.p_tags) {
        filters.p_tags = this.$route.query.p_tags
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
      }).finally(() => {
        this.loaded = true
        if (!this.firstLoad) {
          VueScrollTo.scrollTo(this.$el, 1000, {})
        }
        this.firstLoad = false
      })
    }
  },
  components: {
    Post
  }
}

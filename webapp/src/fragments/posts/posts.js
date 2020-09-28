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
    },
    order: {
      type: String,
      default: 'desc' // or 'asc'
    },
    selectableOrder: {
      type: Boolean,
      default: false
    },
    externalPaging: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      posts: [],
      total: null,
      loaded: false,
      totalPages: null,
      firstLoad: true,
      currentPage: 1,
      localOrder: this.order
    }
  },
  watch: {
    page: function () {
      this.currentPage = this.page
      if (this.externalPaging) {
        this.loadPosts()
      }
    },
    currentPage: function () {
      if (this.externalPaging) {
        this.$emit('paged', this.currentPage)
      } else {
        this.loadPosts()
      }
    },
    endpoint: function () {
      this.loadPosts()
    },
    parentId: function () {
      this.loadPosts()
    },
    order: function () {
      this.localOrder = this.order
    },
    localOrder: function () {
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
    orderChanged () {
      this.$emit('orderChanged', this.localOrder)
    },
    loadPosts () {
      this.loaded = false
      // @see http://v2.wp-api.org/reference/posts/
      let filters = {
        page: this.currentPage,
        per_page: this.max,
        order: this.localOrder
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

      let context = this

      postService.query(filters, `/${this.endpoint}`).then((response) => {
        context.posts = response
        if (response.raw && response.data) {
          context.posts = response.data
          context.total = Number(response.headers['x-wp-total'])
          context.totalPages = Number(response.headers['x-wp-totalpages'])
        }
      }).catch(error => {
        console.log(error)
        context.showError(context.$t('posts.thePostListCouldNotBeLoaded'))
      }).finally(() => {
        context.loaded = true
        if (!context.firstLoad) {
          VueScrollTo.scrollTo(this.$el, 1000, {})
        }
        context.firstLoad = false
      })
    }
  },
  components: {
    Post
  }
}

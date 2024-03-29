import relatedService from './related-service'
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
    max: {
      type: Number,
      default: 10
    },
    page: {
      type: Number,
      default: 1
    },
    pagination: {
      type: Boolean,
      default: true
    },
    contentId: {
      type: Number,
      required: true
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
    },
    '$route': function () {
      this.loadPosts()
    }
  },
  computed: {
    boxTitle () {
      return this.title || this.$t('related.title')
    },
    postList () {
      return this.posts
    }
  },
  methods: {
    loadPosts () {
      // @see http://v2.wp-api.org/reference/posts/
      let filters = {
        page: this.currentPage,
        per_page: this.max
      }

      if (this.exclude.length > 0) {
        filters.exclude = this.exclude.join(',')
      }
      let service = relatedService.clone()
      let endpoint = service.getEndPoint().replace('<contentId>', this.contentId)
      service.setEndPoint(endpoint)

      let context = this
      service.query(filters).then((response) => {
        context.posts = response
        if (response.raw && response.data) {
          context.posts = response.data
          context.total = Number(response.headers['x-wp-total'])
          context.totalPages = Number(response.headers['x-wp-totalpages'])
        }
      }).catch(error => {
        console.log(error)
        context.showError(context.$t('related.theRelatedListCouldNotBeLoaded'))
      })
    }
  },
  components: {
    Post
  }
}

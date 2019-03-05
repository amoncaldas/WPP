import commentService from './comment-service'

export default {
  name: 'comments',
  created () {
    this.currentPage = this.page
    this.loadData()
  },
  watch: {
    $route: function () {
      this.post = null
      setTimeout(() => {
        this.loadData()
      }, 100)
    }
  },
  props: {
    postId: {
      required: false
    },
    page: {
      type: Number,
      default: 1
    },
    max: {
      type: Number,
      default: 10
    },
  },
  data () {
    return {
      comments: [],
      total: null,
      totalPages: null,
      currentPage: null
    }
  },

  methods: {
    loadData () {
      let context = this

      let filters = {
        post: this.postId,
        page: this.currentPage,
        per_page: this.max,
      }
      commentService.query(filters).then((response) => {
        context.comments = response
        if (response.raw && response.data) {
          context.comments = response.data
          context.total = Number(response.headers['x-wp-total'])
          context.totalPages = Number(response.headers['x-wp-totalpages'])
        }
      }).catch(error => {
        console.log(error)
        context.showError(this.$t('comments.theCommentsCouldNotBeLoaded'))
      })
    }
  },
  components: {

  }
}

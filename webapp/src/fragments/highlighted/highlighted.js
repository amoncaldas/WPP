import highlightedtService from './highlighted-service'
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
    position: {
      type: String,
      default: 'top'
    },
    contentId: {
      type: Number,
      required: true
    }
  },
  data () {
    return {
      posts: [],
      hightlightTitle: null
    }
  },
  watch: {
    '$route': function () {
      this.loadPosts()
    }
  },
  computed: {
    boxTitle () {
      return this.hightlightTitle || this.title || this.$t('highlighted.title')
    }
  },
  methods: {
    loadPosts () {
      // @see http://v2.wp-api.org/reference/posts/
      let service = highlightedtService
      let endpoint = service.getEndPoint().replace('<contentId>', this.contentId)
      endpoint = `${endpoint}/${this.position}`
      service.setEndPoint(endpoint)
      service.query({}).then((response) => {
        this.posts = response
        if (response.raw && response.data) {
          this.posts = response.data
          this.hightlightTitle = response.headers['x-wpp-title']
        }
      }).catch(error => {
        console.log(error)
        this.showError(this.$t('highlighted.theHightlightedListCouldNotBeLoaded'))
      })
    }
  },
  components: {
    Post
  }
}

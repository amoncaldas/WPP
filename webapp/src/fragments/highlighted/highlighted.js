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
    },
    columns () {
      let items = this.posts.length
      if (items === 0) {
        return this.columnsPerPost
      }
      if (items === 4 || items === 2) {
        return 6
      }
      return 4
    }
  },
  methods: {
    loadPosts () {
      // @see http://v2.wp-api.org/reference/posts/
      let service = highlightedtService.clone()
      let endpoint = service.getEndPointTemplate().replace('<contentId>', this.contentId)

      // Build a new endpoint
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

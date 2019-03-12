import searchService from './search-service'
import Post from '@/fragments/post/Post'

export default {
  data () {
    return {
      term: '',
      results: [],
      max: 5,
      total: null,
      totalPages: null,
      currentPage: 1
    }
  },
  watch: {
    '$route': function () {
      this.loadData()
    }
  },
  components: {
    Post
  },
  methods: {
    search () {
      this.$route.query.s = this.term
    },
    loadData() {
      if (this.$route.query.s) {
        this.term = this.$route.query.s
        searchService.query({'s': this.term, page: this.currentPage, per_page: this.max}).then((response)=> {
          this.results = response
          if (response.raw && response.data) {
            this.results = response.data
            this.total = Number(response.headers['x-wp-total'])
            this.totalPages = Number(response.headers['x-wp-totalpages'])
          }
        })
      }
    }
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('search.pageTitle'))

    this.loadData()

  }
}

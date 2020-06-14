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
      currentPage: 1,
      section: null,
      searched: false,
      debounceTimeoutId: null
    }
  },
  watch: {
    currentPage: function () {
      this.search()
    },
    '$route.query': {
      handler: function () {
        this.loadData()
      },
      deep: true
    }
  },
  components: {
    Post
  },
  computed: {
    searchableSections () {
      let context = this
      let searchable = this.lodash.filter(this.$store.getters.sections, (s) => {
        return s.path !== '/' && !s.extra.not_listed && s.locale === context.$store.getters.locale
      })
      return searchable
    },
    searchInputColumns () {
      let columns = this.searchableSections.length > 1 ? 8 : 12
      return columns
    }
  },
  methods: {
    search () {
      let context = this
      clearTimeout(this.debounceTimeoutId)
      this.debounceTimeoutId = setTimeout(function () {
        context.doSearch()
      }, 1000)
    },
    doSearch () {
      let query = {s: this.term, page: this.currentPage}
      if (this.section) {
        query.section = this.section
      }
      if (this.$route.query.l) {
        query.l = this.$route.query.l
      }
      this.$router.push({name: 'HomeOrSearch', query: query})
    },
    loadData () {
      if (this.$route.query.s) {
        this.term = this.$route.query.s
        this.section = Number(this.$route.query.section)

        // Build the filters object
        let filters = {s: this.term, page: this.currentPage, per_page: this.max}
        if (this.$route.query.section) {
          filters.section = this.$route.query.section
        }
        this.searched = false
        searchService.query(filters).then((response) => {
          this.results = response
          this.searched = true
          if (response.raw && response.data) {
            this.results = response.data
            this.total = Number(response.headers['x-wp-total'])
            this.totalPages = Number(response.headers['x-wp-totalpages'])
          }
        })
      } else {
        this.results = []
        this.totalPages = 0
      }
    }
  },
  created () {
    this.currentPage = this.$route.query.page || this.currentPage
    this.loadData()
  }
}

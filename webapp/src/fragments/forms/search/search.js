import searchService from './search-service'
import Post from '@/fragments/post/Post'
import wpp from '@/support/wpp'

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
    postType: function () {
      this.localPostType = this.postType
    },
    '$route.query': {
      handler: function () {
        this.loadData()
      },
      deep: true
    }
  },
  props: {
    postType: {
      type: String,
      default: null
    },
    sectionFilter: {
      type: Boolean,
      default: false
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
      let columns = this.sectionFilter && this.searchableSections.length > 1 ? 8 : 12
      return columns
    },
    resultsTitle () {
      var title = this.$t('searchComponent.results')
      if (this.localPostType) {
        let archiveTranslated = wpp.getArchiveTranslated(this.localPostType).toLowerCase()
        let forStr = this.$t('searchComponent.for')
        title = `${title} ${forStr} ${archiveTranslated}`
      }
      if (this.sectionFilter && this.section) {
        let section = this.lodash.find(this.$store.getters.sections, (s) => {
          return s.id !== this.section
        })
        let inStr = this.$t('searchComponent.in')
        title = `${title} ${inStr} ${section.title}`
      }
      return title
    },
    placeHolder () {
      let placeHolder = this.$t('searchComponent.placeholder')
      if (this.localPostType) {
        let typeTitle = wpp.getArchiveTranslated(this.localPostType)
        placeHolder = `${this.$t('searchComponent.search')} ${this.$t('searchComponent.for')} ${typeTitle}`
      }
      return placeHolder
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
    runSearchWithEnter (event) {
      if (event.key === 'Enter') {
        this.doSearch()
      }
    },
    doSearch () {
      if (this.term) {
        let query = {s: this.term, page: this.currentPage}
        if (this.section) {
          query.section = this.section
        }
        if (this.$route.query.l) {
          query.l = this.$route.query.l
        }
        if (this.$route.query.post_type) {
          query.post_type = this.$route.query.post_type
        }
        if (this.localPostType) {
          query.post_type = this.localPostType
        }
        this.$router.push({name: 'HomeOrSearch', query: query})
      } else {
        this.showError(this.$t('searchComponent.typeSomethingtoSearch'))
      }
    },
    loadData () {
      if (this.$route.query.s) {
        this.term = this.$route.query.s
        this.section = Number(this.$route.query.section) // section id or null

        // Build the filters object
        let filters = {s: this.term, page: this.currentPage, per_page: this.max}
        if (this.$route.query.section) {
          filters.section = this.$route.query.section
        }
        if (this.$route.query.post_type) {
          filters.post_type = this.$route.query.post_type
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
    this.localPostType = this.$route.query.post_type || this.postType
    this.section = this.$route.query.section ? Number(this.$route.query.section) : null
    this.loadData()
  }
}

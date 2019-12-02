import postService from '@/shared-services/post-service'
import Media from '@/fragments/media/Media'
import PostMap from '@/fragments/post-map/PostMap'
import Gallery from '@/fragments/gallery/Gallery'
import Comments from '@/fragments/comments/Comments'
import utils from '@/support/utils'
import AuthorAndPlace from './components/author-and-place/AuthorAndPlace'
import Sharer from '@/fragments/sharer/Sharer'
import ReportError from '@/fragments/report-error/ReportError'

export default {
  name: 'post',
  created () {
    this.renderAsPage = this.isPage
    let pageTypes = this.$store.getters.options.page_like_types
    pageTypes = Array.isArray(pageTypes) ? pageTypes : [pageTypes]

    if (pageTypes.includes(this.$store.getters.postTypeEndpoint)) {
      this.renderAsPage = true
    }
    this.post = this.postData
    this.comments_count = 0

    this.loadAddings()
  },
  props: {
    isPage: {
      default: false
    },
    postData: {
      required: true
    },
    noTopBorder: {
      default: false
    },
    mode: {
      type: String,
      default: 'block' // compact, list, single, block
    },
    explicitLocale: {
      type: Boolean,
      default: false
    },
    showType: {
      default: false
    }
  },
  data () {
    return {
      post: null,
      galleryImageIndex: null,
      renderAsPage: false,
      showReportError: false,
      prepend: null,
      append: null
    }
  },
  computed: {
    featuredMedia () {
      if (this.post._embedded && this.post._embedded['wp:featuredmedia']) {
        let media = this.post._embedded['wp:featuredmedia'][0]
        return media
      }
    },
    hasPlaces () {
      return this.post.places && Object.keys(this.post.places).length > 0
    },
    related () {
      if (this.post && this.post.extra && this.post.extra.related && Array.isArray(this.post.extra.related)) {
        return this.post.extra.related
      }
      return []
    },
    title () {
      if (this.post.title.rendered) {
        return this.post.title.rendered
      }
      return this.post.title
    },
    excerpt () {
      let excerpt = this.content
      if (this.post.excerpt) {
        excerpt = this.post.excerpt
        if (excerpt.rendered !== undefined) {
          excerpt = excerpt.rendered
        }
      }

      excerpt = excerpt.replace(/<(?:.|\n)*?>/gm, '')
      let maxLength = this.mode === 'compact' ? 150 : 300
      if (excerpt.length > maxLength) {
        excerpt = excerpt.substring(0, maxLength)
        return excerpt.length > 0 ? `${excerpt} [...]` : excerpt
      }

      return excerpt
    },
    link () {
      if (this.post.extra.custom_link && this.post.extra.custom_link.length > 0 && this.post.extra.custom_link !== ' ') {
        return this.post.extra.custom_link
      }
      return this.post.path
    },

    content () {
      let content = ''
      if (this.post.content) {
        content = this.post.content.rendered !== undefined ? this.post.content.rendered : this.post.content
      } else if (this.post.extra && this.post.extra.html_content) {
        content = this.post.extra.html_content
      }
      if (!content && this.post.excerpt) {
        if (this.post.excerpt.rendered !== undefined) {
          return this.post.excerpt.rendered
        }
        return this.post.excerpt
      }
      return content
    },

    categories () {
      let categories = this.getTerms('category')
      return categories
    },
    tags () {
      let categories = this.getTerms('post_tag')
      return categories
    },
    type () {
      let trans = this.$store.getters.options.post_type_translations[this.post.type]
      if (trans && trans[this.$store.getters.locale]) {
        return trans[this.$store.getters.locale].title
      }
      return this.post.type
    },
    postDate () {
      let postDate = this.post.extra.custom_post_date || this.post.date
      return this.formatDateTime(postDate)
    },
    commentsTabTitle () {
      let title = this.post.comments_count > 0 ? `${this.$t('post.comments')} (${this.post.comments_count})` : this.$t('post.comments')
      return title
    }
  },
  methods: {
    formatDate (date) {
      return utils.getFormattedDate(date)
    },
    formatDateTime (date) {
      return utils.getFormattedDateTime(date)
    },
    commentsCountUpdated (amount) {
      this.post.comments_count = amount
    },
    placeClicked (place) {
      if (place && place.link) {
        var parser = document.createElement('a')
        parser.href = place.link
        this.$router.push(parser.pathname)
      }
    },

    getTermUri (term, queryVar) {
      let uri = this.buildLink(`/${this.$store.getters.postTypeEndpoint}?${queryVar}=${term.slug}`)
      return uri
    },

    getTerms (type) {
      let termsFound = []
      if (this.post._embedded && this.post._embedded['wp:term'] && this.post._embedded['wp:term'].length > 0) {
        for (let termKey in this.post._embedded['wp:term']) {
          let terms = this.post._embedded['wp:term'][termKey]
          for (let key in terms) {
            let term = terms[key]
            if (term.taxonomy === type) {
              termsFound.push(term)
            }
          }
        }
      }
      return termsFound
    },
    loadAddings () {
      if (this.post.extra.prepend) {
        let id = Array.isArray(this.post.extra.prepend) && this.post.extra.prepend.length > 0 ? this.post.extra.prepend[0] : this.post.extra.prepend
        postService.get(`addings/${id}`).then((prepend) => {
          this.prepend = prepend.content.rendered
        })
      }
      if (this.post.extra.append) {
        let id = Array.isArray(this.post.extra.append) && this.post.extra.append.length > 0 ? this.post.extra.append[0] : this.post.extra.append
        postService.get(`addings/${id}`).then((append) => {
          this.append = append.content.rendered
        })
      }
    },
    navigateToSingle () {
      if (this.mode !== 'single') {
        // if the target post has the same locale, do a soft redirect
        if (this.post.locale === 'neutral' || this.post.locale === this.$store.getters.locale) {
          this.$router.push({path: this.buildLink(this.link)})
        } else { // if not reload the page
          window.location.href = this.link
        }
      }
    }
  },
  components: {
    Media,
    PostMap,
    Gallery,
    Comments,
    AuthorAndPlace,
    Sharer,
    ReportError
  },
  beforeCreate: function () {
    this.$options.components.Related = require('@/fragments/related/Related.vue').default
    this.$options.components.Highlighted = require('@/fragments/highlighted/Highlighted.vue').default
  }
}

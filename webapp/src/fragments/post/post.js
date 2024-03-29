import postService from '@/shared-services/post-service'
import Media from '@/fragments/media/Media'
import WppMap from '@/fragments/wpp-map/WppMap'
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

    if (pageTypes.indexOf(this.$store.getters.postTypeEndpoint) > -1) {
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
      let has = this.post && this.post.extra.has_places
      return has
    },
    /**
     * Build map object
     * based on post properties
     * @returns {*} mapData {places: Array, title: String, extra: Object}
     */
    placesMapData () {
      let mapData = {
        places: this.post.places
      }
      // Define the map title
      // based on the map title field,
      // the only place name or the
      // default map title
      if (this.post.extra.map_title) {
        mapData.title = this.post.extra.map_title
      } else {
        if (this.post.places.length === 1) {
          let keys = Object.keys(this.post.places)
          let firstKey = keys[0]
          mapData.title = this.post.places[firstKey].title
        } else {
          mapData.title = this.$t('post.defaultMapTitle')
        }
      }
      if (this.post.extra) {
        mapData.extra = {
          zoom: this.post.extra.zoom,
          tiles_provider_id: this.post.extra.tiles_provider_id
        }
      }
      return mapData
    },
    hasMaps () {
      if (this.post.extra && this.post.extra.maps && Object.keys(this.post.extra.maps).length > 0) {
        return true
      }
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
      let excerpt = ''
      if (this.post.excerpt) {
        excerpt = this.post.excerpt
        if (typeof excerpt === 'object' && excerpt.rendered !== undefined) {
          excerpt = excerpt.rendered
        }
      } else {
        excerpt = this.content
        // If we are using the beginning of the
        // content as excerpt, remove any html
        excerpt = excerpt.replace(/<(?:.|\n)*?>/gm, '')
        let maxLength = this.mode === 'compact' ? 150 : 300

        // if the excerpt is too long shrink it
        if (excerpt.length > maxLength) {
          excerpt = excerpt.substring(0, maxLength)
          return excerpt.length > 0 ? `${excerpt} [...]` : excerpt
        }
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
      let tags = this.getTerms('post_tag')
      return tags
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
      if (this.post && this.post.extra.prepend) {
        let id = Array.isArray(this.post.extra.prepend) && this.post.extra.prepend.length > 0 ? this.post.extra.prepend[0] : this.post.extra.prepend
        postService.get(`addings/${id}`).then((prepend) => {
          this.prepend = prepend.content.rendered
        })
      }
      if (this.post && this.post.extra.append) {
        let id = Array.isArray(this.post.extra.append) && this.post.extra.append.length > 0 ? this.post.extra.append[0] : this.post.extra.append
        postService.get(`addings/${id}`).then((append) => {
          this.append = append.content.rendered
        })
      }
    },
    navigateToSingle () {
      if (this.mode !== 'single') {
        this.routeToLink(this.link, this.post.extra.target_blank)
      }
    }
  },
  components: {
    Media,
    Gallery,
    Comments,
    AuthorAndPlace,
    Sharer,
    ReportError,
    WppMap
  },
  beforeCreate: function () {
    this.$options.components.Related = require('@/fragments/related/Related.vue').default
    this.$options.components.Highlighted = require('@/fragments/highlighted/Highlighted.vue').default
  }
}

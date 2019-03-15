import postService from '@/shared-services/post-service'
import Media from '@/fragments/media/Media'
import PostMap from '@/fragments/post-map/PostMap'
import Gallery from '@/fragments/gallery/Gallery'
import Comments from '@/fragments/comments/Comments'
import wppRouter from '@/support/wpp-router'
import utils from '@/support/utils'

export default {
  name: 'post',
  created () {
    this.renderAsPage = this.isPage
    let treatAsPage = wppRouter.getPageLikeEndPoints()
    if (treatAsPage.includes(this.$store.getters.postTypeEndpoint)) {
      this.renderAsPage = true
    }
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
    isPage: {
      default: false
    },
    postName: {
      required: false
    },
    postData: {
      required: false
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
      renderAsPage: false
    }
  },
  computed: {
    featuredMedia () {
      if (this.post._embedded && this.post._embedded['wp:featuredmedia']) {
        let media = this.post._embedded['wp:featuredmedia'][0]
        return media
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
      let maxLength = this.mode === 'compact' ? 150 : 300
      if (this.post.excerpt) {
        return this.post.excerpt
      } else if (this.content.length > maxLength) {
        let subContent = this.content.replace(/<(?:.|\n)*?>/gm, '').substring(0, maxLength)
        return subContent.length > 0 ? `${subContent} [...]` : subContent
      } else {
        return this.content.replace(/<(?:.|\n)*?>/gm, '')
      }
    },
    link () {
      if (this.post.extra && this.post.extra.custom_link) {
        return this.post.extra.custom_link
      }
      return this.post.path
    },

    content () {
      let content = ''
      if (this.post.content) {
        content = this.post.content.rendered !== undefined ? this.post.content.rendered : this.post.content
      } else if (this.post.extra && this.post.extra.content) {
        content = this.post.extra.content
      }
      if (!content) {
        console.log('a');
      }
      return content
    },
    author () {
      return this.post._embedded.author[0].name
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
    }
  },
  methods: {
    loadData () {
      if (this.postData) {
        this.post = this.postData
      } else {
        let context = this
        let endpoint = this.$store.getters.postTypeEndpoint
        let endpointAppend = null
        if (this.postId) {
          endpointAppend = `${endpoint}/${this.postId}?_embed=1`
        } else if (this.postName) {
          endpointAppend = `${endpoint}?slug=${this.postName}&_embed=1`
        }
        postService.get(endpointAppend).then((post) => {
          if (Array.isArray(post)) {
            context.post = post[0]
          } else {
            context.post = post
          }
          // If in single mdoe, set the site title
          if (this.mode === 'single') {
            this.eventBus.$emit('titleChanged', `${this.title} | ${this.$store.getters.options.site_title}`)
          }
        }).catch(error => {
          console.log(error)
          context.showError(this.$t('post.thePostCouldNotBeLoaded'))
        })
      }
    },

    formatDate (date) {
      return utils.getFormattedDate(date)
    },
    formatDateTime (date) {
      return utils.getFormattedDateTime(date)
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
      if (this.post._embedded['wp:term'] && this.post._embedded['wp:term'].length > 0) {
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
    }
  },
  components: {
    Media,
    PostMap,
    Gallery,
    Comments
  },
  beforeCreate: function () {
    this.$options.components.Related = require('@/fragments/related/Related.vue').default
  }
}

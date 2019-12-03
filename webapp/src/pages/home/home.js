import SectionsMap from '@/fragments/sections-map/SectionsMap'
import PostMap from '@/fragments/post-map/PostMap'
import Posts from '@/fragments/posts/Posts'
import Slider from '@/fragments/slider/Slider'
import Sections from '@/fragments/sections/Sections'
import Section from '@/support/section'
import Highlighted from '@/fragments/highlighted/Highlighted.vue'

export default {
  data: () => ({
    loaded: false,
    listingPosts: [],
    compactListingPosts: [],
    currentSection: null
  }),
  components: {
    SectionsMap,
    Posts,
    Slider,
    PostMap,
    Sections,
    Highlighted
  },
  created () {
    // emit the an event catch by root App component
    // telling it to update the page title
    let title = this.$store.getters.options.site_title
    if (this.$store.getters.options.site_title_translations && this.$store.getters.options.site_title_translations[this.$store.getters.locale]) {
      title = this.$store.getters.options.site_title_translations[this.$store.getters.locale]
    }
    this.eventBus.$emit('titleChanged', title)

    this.loadData()
    this.eventBus.$on('localeChanged', () => {
      this.loadData()
    })
  },
  watch: {
    $route: {
      handler: function () {
        this.loaded = false
        setTimeout(() => {
          this.loadData()
        }, 100)
      },
      deep: true
    }
  },
  methods: {
    loadData () {
      this.currentSection = Section.getCurrentHomeSection()
      this.$store.commit('currentSection', this.currentSection)
      this.listingPosts = Section.getListingPosts()
      this.compactListingPosts = Section.getCompactListingPosts()
      if (this.currentSection.locale !== 'neutral') {
        this.eventBus.$emit('setLocaleFromContentLocale', this.currentSection.locale)
      }
      this.loaded = true
    },
    placeClicked (place) {
      if (place && place.link) {
        var parser = document.createElement('a')
        parser.href = place.link
        this.$router.push(parser.pathname)
      }
    }
  },
  computed: {
    max () {
      let max = this.currentSection.extra.max_listing_posts !== undefined ? this.currentSection.extra.max_listing_posts : 4
      return Number(max)
    },

    maxCompact () {
      let max = this.currentSection.extra.max_compact_listing_posts !== undefined ? this.currentSection.extra.max_compact_listing_posts : 4
      return Number(max)
    }
  }
}

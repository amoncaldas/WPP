import WppMap from '@/fragments/wpp-map/WppMap'
import Posts from '@/fragments/posts/Posts'
import Slider from '@/fragments/slider/Slider'
import Section from '@/support/section'
import Sections from '@/fragments/sections/Sections'
import Highlighted from '@/fragments/highlighted/Highlighted.vue'

export default {
  data: () => ({
    ready: false,
    listingPosts: [],
    compactListingPosts: [],
    currentSection: null
  }),
  components: {
    WppMap,
    Posts,
    Slider,
    Sections,
    Highlighted
  },
  created () {
    this.loadData()
  },
  watch: {
    '$store.getters.currentSection': {
      handler: function () {
        this.ready = false
        this.loadData()
      },
      deep: true
    }
  },
  methods: {
    placeClicked (place) {
      if (place && place.link) {
        var parser = document.createElement('a')
        parser.href = place.link
        this.$router.push(parser.pathname)
      }
    },
    loadData () {
      this.currentSection = this.$store.getters.currentSection
      this.listingPosts = Section.getListingPosts()
      this.compactListingPosts = Section.getCompactListingPosts()

      // Emit the an event catch by root App component
      // telling it to update the page title
      if (this.currentSection.locale !== 'neutral') {
        this.eventBus.$emit('setLocaleFromContentLocale', this.currentSection.locale)
      }
      let title = this.currentSection.title.rendered
      this.eventBus.$emit('titleChanged', title)
      this.ready = true
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
    },
    hasMaps () {
      if (this.currentSection.extra.maps && Object.keys(this.currentSection.extra.maps).length > 0) {
        return true
      }
    }
  }
}

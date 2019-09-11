import PostMap from '@/fragments/post-map/PostMap'
import Posts from '@/fragments/posts/Posts'
import Slider from '@/fragments/slider/Slider'
import Section from '@/support/section'
import Sections from '@/fragments/sections/Sections'
import Highlighted from '@/fragments/highlighted/Highlighted.vue'

export default {
  data: () => ({
    valid: false,
    listingPosts: [],
    compactListingPosts: [],
    currentSection: null
  }),
  components: {
    PostMap,
    Posts,
    Slider,
    Sections,
    Highlighted
  },
  created () {
    this.currentSection = this.$store.getters.currentSection
    this.listingPosts = Section.getListingPosts()
    this.compactListingPosts = Section.getCompactListingPosts()

    // Emit the an event catch by root App component
    // telling it to update the page title
    let title = `${this.currentSection.title.rendered} | ${this.$store.getters.options.site_title}`
    if (this.currentSection.locale !== 'neutral') {
      this.eventBus.$emit('setLocaleFromContentLocale', this.currentSection.locale)
    }
    this.eventBus.$emit('titleChanged', title)
  },
  methods: {
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

import SectionsMap from '@/fragments/sections-map/SectionsMap'
import Posts from '@/fragments/posts/Posts'
import Slider from '@/fragments/slider/Slider'
import Section from '@/support/section'

export default {
  data: () => ({
    valid: false,
    homePostYpes: [],
    currentSection: null
  }),
  components: {
    SectionsMap,
    Posts,
    Slider
  },
  created () {
    // emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$store.getters.options.site_title)
    this.loadData()
    this.eventBus.$on('localeChanged', () => {
      this.loadData()
    })
  },
  computed: {
    listingPosts () {
      return this.homePostYpes
    }
  },
  methods: {
    loadData () {
      this.currentSection = Section.getCurrentHomeSection()
      this.$store.commit('currentSection', this.currentSection)
      this.homePostYpes = Section.getListingPosts()
    }
  },

}

import SectionsMap from '@/fragments/sections-map/SectionsMap'
import Posts from '@/fragments/posts/Posts'
import Section from '@/support/section'

export default {
  data: () => ({
    valid: false,
    activeTab: '0',
    listPostEndpoints: [],
    currentSection: null
  }),
  components: {
    SectionsMap,
    Posts
  },
  created () {
    if (this.$route.query.tab) {
      this.activeTab = this.$route.query.tab
    }

    this.currentSection = Section.getCurrentSection()
    this.listPostEndpoints = Section.getListingPosts()

    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$store.getters.options.site_title)
  },
  methods: {
  }
}

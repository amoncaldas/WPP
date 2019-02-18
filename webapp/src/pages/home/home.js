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
    // emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$store.getters.options.site_title)
    this.loadData()
    this.eventBus.$on('localeChanged', () => {
      this.loadData()
    })
  },
  methods: {
    loadData () {
      this.currentSection = Section.getCurrentHomeSection()
      this.$store.commit('currentSection', this.currentSection)
      this.listPostEndpoints = Section.getListingPosts()
    }
  },

}

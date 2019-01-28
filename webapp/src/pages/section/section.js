import PostMap from '@/fragments/post-map/PostMap'
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
    PostMap,
    Posts
  },
  created () {
    this.currentSection = Section.getCurrentSection()
    this.listPostEndpoints = Section.getListingPosts()

    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$store.getters.options.site_title)
  },
  methods: {
  }
}

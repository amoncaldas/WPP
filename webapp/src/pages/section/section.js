import PostMap from '@/fragments/post-map/PostMap'
import Posts from '@/fragments/posts/Posts'
import Section from '@/support/section'

export default {
  data: () => ({
    valid: false,
    activeTab: '0',
    homePostYpes: [],
    currentSection: null
  }),
  components: {
    PostMap,
    Posts
  },
  computed: {
    computed: {
      listingPosts () {
        return this.homePostYpes
      }
    },
  },
  created () {
    this.currentSection = Section.getCurrentSection()
    this.homePostYpes = Section.getListingPosts()

    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$store.getters.options.site_title)
  },
  methods: {
  }
}

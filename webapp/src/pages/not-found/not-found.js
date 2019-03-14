import Search from '@/fragments/forms/search/Search'

export default {
  data: () => ({
    loaded: false
  }),
  computed: {
  },
  methods: {
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('notFound.pageTitle'))
  },
  components: {
    Search
  }
}

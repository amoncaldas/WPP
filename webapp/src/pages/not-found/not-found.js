import NotFoundComponent from '@/fragments/not-found/NotFound'

export default {
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('notFound.pageTitle'))
  },
  components: {
    NotFoundComponent
  }
}

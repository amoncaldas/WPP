import Subscribe from '@/fragments/subscribe/Subscribe'

export default {
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('subscribe.pageTitle'))
  },
  components: {
    Subscribe
  }
}

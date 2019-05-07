import Profile from './tabs/profile/Profile'

export default {
  data: () => ({
    valid: false,
    activeTab: '0'
  }),
  components: {
    userProfile: Profile
  },
  created () {
    if (this.$route.query.tab) {
      this.activeTab = this.$route.query.tab
    }
    // emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('myAccount.pageTitle'))
  }
}

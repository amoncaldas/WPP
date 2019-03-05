import Signup from '@/fragments/signup/Signup'

export default {

  methods: {
    afterSignup () {
      this.$router.replace({path: '/profile', query: {tab: '1'}})
    }
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('register.pageTitle'))
  },
  components: {
    Signup
  }
}

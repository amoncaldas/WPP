import Authentication from '@/fragments/authentication/Authentication'

export default {
  created () {
    this.eventBus.$emit('titleChanged', this.$t('login.pageTitle'))
  },
  components: {
    Authentication
  },
  methods: {
    redirectToProfile () {
      this.$router.replace('/profile')
    }
  },
}

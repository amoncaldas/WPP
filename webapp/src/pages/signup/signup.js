
// https://itnext.io/how-to-use-google-recaptcha-with-vuejs-7756244400da
import VueRecaptcha from 'vue-recaptcha'

import UserForm from '@/fragments/forms/user/User'
import socialAuth from '@/common/social-auth'
import auth from '@/support/auth'

export default {
  data () {
    return {
      verifiedCaptcha: false,
      resource: {},
      passVisibility: true,
      sectors: [],
      context: null
    }
  },
  methods: {
    submit (context) {
      this.context = context
      this.showInfo(this.$t('signup.processingRegistration'), {timeout: 6000})
      this.$refs.recaptcha.execute()
    },
    onCaptchaVerified (recaptchaToken) {
      this.showInfo(this.$t('signup.processingRegistration'), {timeout: 6000})
      this.context.resource.recaptchaToken = recaptchaToken
      const self = this
      self.$refs.recaptcha.reset()
      self.verifiedCaptcha = true
      this.context.save()
    },
    onCaptchaExpired () {
      this.$refs.recaptcha.reset()
      this.verifiedCaptcha = false
    },
    /**
     * Authenticate a user using a social oauth provider
     * @param string provider (only github supported for now)
     *
     * This authentication uses vue-authenticate and the local script
     * @/common/social-auth that authenticate the user on github and
     * returns the user data. @see @/common/social-auth to read the details
     */
    socialRegistration: function (provider) {
      socialAuth.oauthViaRedirect(provider, 'signup')
    }
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('signup.pageTitle'))

    socialAuth.checkAndProceedOAuth((userData) => {
      userData = auth.parseUserData(userData)

      this.$store.dispatch('login', userData).then(() => {
        // Tab 1 correspond to the profile tab, so after registering, the user is redirected to the home/profile
        // so s/he can complete the profile, if s/he want
        this.$router.replace({path: '/home', query: {tab: '1'}})
      }).catch(error => {
        console.log('login error', error)
      })
    })
  },
  components: {
    VueRecaptcha,
    UserForm
  }
}

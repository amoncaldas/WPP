import passwordResetRequestService from './request-service'

export default {
  data: () => ({
    usernameOrEmail: ''
  }),
  computed: {
    userNameOrEmailRules () {
      return [
        v => !!v || this.$t('reset.usernameOrEmailRequired')
      ]
    }
  },
  methods: {
    goToLogin () {
      this.$router.push({name: 'Login'})
    },

    /**
     * As in the back-end we are using wordpress and a specific plugin for triggering user password reset link, in this page
     * we are not using te wp-api, but instead we instantiate a javascript FormData object, fill it with the expected data by
     * the plugin and send it. In the future a custom rest end-point should be created to trigger the same action so we only
     * use rest requests.
     *
     */
    submit () {
      let data = { login: this.usernameOrEmail }

      passwordResetRequestService.customQuery({verb: 'post', data: data}).then((response) => {
        // we are able to determine if the request succeeded analyzing the response data
        if (response.httpStatusCode === 404) {
          this.showError(this.$t('reset.invalidUserNameOrEmail'))
        } else {
          this.showSuccess(this.$t('reset.checkYourEmail'), {mode: 'multi-line'})
        }
      })
    }
  },
  created () {
    // emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('reset.pageTitle'))
  }
}

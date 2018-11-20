import httpApi from '@/common/http-api'
import socialAuth from '@/common/social-auth'
import auth from '@/support/auth'

/* JWT AUTH api end point used to retrieve/send data */
const authEndpoint = 'jwt-auth/v1/token' // will be appended to the base url defined in the @/config.js file

export default {
  data: () => ({
    username: '',
    password: '',
    hidePass: true
  }),
  computed: {
    userNameRules () {
      return [
        v => !!v || this.$t('login.usernameRequired')
      ]
    },
    passwordRules () {
      return [
        v => !!v || this.$t('login.passwordRequired')
      ]
    }
  },
  methods: {
    goToReset () {
      this.$router.push({name: 'PasswordRequest'})
    },
    submit () {
      if (this.$refs.form.validate()) {
        localStorage.clear()
        var authData = {
          username: this.username,
          password: this.password
        }

        httpApi.post(authEndpoint, authData).then(userData => {
          if (userData.data.token) {
            auth.setUserAndRedirect(this, userData.data)
          } else {
            this.showError(this.$t('login.invalidCredentials'))
          }
        })
        .catch(error => {
          if (error.data && error.data.message) {
            this.showError(error.data.message)
          } else {
            this.showError(this.$t('login.failWhileTryingToLogin'))
            console.log(error)
          }
        })
      } else {
        this.showError(this.$t('login.invalidCredentials'))
      }
    },

    /**
     * Authenticate a user using a social oauth provider
     * @param string provider (only github supported for now)
     *
     * This authentication uses vue-authenticate and the local script
     * @/common/social-auth that authenticate the user on github and
     * returns the user data. @see @/common/social-auth to read the details
     */
    socialAuthentication: function (provider) {
      socialAuth.oauthViaRedirect(provider, 'login')
    }
  },
  created () {
    socialAuth.checkAndProceedOAuth((userData) => {
      auth.setUserAndRedirect(this, userData)
    })
    // emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('login.pageTitle'))
  }
}

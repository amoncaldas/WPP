import socialAuth from '@/common/social-auth'
import CrudHttpOptions from '@/common/crud-http-options'
import {CrudHttpApi} from 'vue-rest-crud'

import auth from '@/support/auth'

/* JWT AUTH api end point used to retrieve/send data */
const authEndpoint = 'jwt-auth/v1/token' // will be appended to the base url defined in the @/config.js file

export default {
  data: () => ({
    username: '',
    password: '',
    hidePass: true
  }),
  props: {
    onAuthenticate: {
      type: Function,
      required: true
    },
    topBorder: {
      default: true,
      type: Boolean
    }
  },
  computed: {
    userNameRules () {
      return [
        v => !!v || this.$t('authentication.usernameRequired')
      ]
    },
    passwordRules () {
      return [
        v => !!v || this.$t('authentication.passwordRequired')
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

        let crudHttpApi = new CrudHttpApi(CrudHttpOptions)

        crudHttpApi.http.post(authEndpoint, authData).then(userData => {
          if (userData.data.token) {
            auth.setUserAndRedirect(this, userData.data, this.onAuthenticate)
          } else {
            this.showError(this.$t('authentication.invalidCredentials'))
          }
        })
        .catch(error => {
          this.showError(this.$t('authentication.failWhileTryingToLogin'))
          console.log(error)
        })
      } else {
        this.showError(this.$t('authentication.invalidCredentials'))
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
  }
}

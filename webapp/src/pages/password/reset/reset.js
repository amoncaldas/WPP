import passwordResetService from './reset-service'
import VueRestCrud from 'vue-rest-crud'

export default {
  data: () => ({
    valid: true,
    password: null,
    confirmPassword: null,
    resetIsValid: false,
    passHidden: true,
    loaded: false,
    ...VueRestCrud.Data // adds: resource, resources, crudReady
  }),
  computed: {
    passwordRules () {
      return [
        (this.resource.password1 !== undefined && this.resource.password1 === this.resource.password2) || this.$t('changePassword.passwordsDoNotMatch')
      ]
    },
    info () {
      return this.loaded === false ? this.$t('changePassword.validatingResetData') : this.$t('changePassword.resetKeyInvalid')
    }
  },
  methods: {
    goToLogin () {
      this.$router.push({name: 'Login'})
    },
    goToRequest () {
      this.$router.push({name: 'PasswordRequest', query: {login: this.resource.login}})
    },
    beforeShowError (errorResponse) {
      switch (errorResponse.status) {
        case 400: // INVALID REQUEST
          errorResponse.message = this.showError(this.$t('changePassword.missingKeyOrUserName'))
          break
        case 409: // CONFLICT - passwords do not match
          errorResponse.message = this.showError(this.$t('changePassword.passwordsDoNotMatch'))
          break
        case 404: // NOT FOUND - the pair user login and key was not found
          errorResponse.message = this.showError(this.$t('changePassword.resetKeyInvalid'))
          break
      }
    },
    afterUpdate () {
      this.$router.push({name: 'Login'})
    },
    validatePasswordReset () {
      let context = this
      this.resource.$post({endPointAppend: '/validate'}).then(() => {
        context.loaded = true
        context.resetIsValid = true
      },
      () => {
        this.showError(this.$t('changePassword.resetKeyInvalid'))
        context.resetIsValid = false
        context.loaded = true
      })
    }
  },
  created () {
    VueRestCrud.Controller.set(this, passwordResetService, { queryOnStartup: false, skipAutoIndexAfterAllEvents: true })

    this.resource.key = this.$route.params.key
    this.resource.login = this.$route.params.login
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('changePassword.pageTitle'))
    this.validatePasswordReset()
  }
}

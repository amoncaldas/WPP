import activateService from './activate-service'
import VueRestCrud from 'vue-rest-crud'

export default {
  data: () => ({
    passHidden: true,
    loaded: false,
    activationError: false,
    ...VueRestCrud.Data // adds: resource, resources, crudReady
  }),
  computed: {
    info () {
      return this.activationError ? this.$t('activate.wrongUserIdOrActivationCode') : this.$t('activate.validatingActivationData')
    }
  },
  methods: {
    resendEmail () {
      let append = '/resend/' + this.resource.userId
      this.resource.$post({endPointAppend: append}).then(() => {
        this.showSuccess(this.$t('activate.activationLinkResent'))
      },
      (error) => {
        if (error.status === 410) {
          // GONE - activation code is not valid anymore because the user is already activated
          this.showError(this.$t('activate.accountAlreadyActivated'))
        } else {
          this.showError(this.$t('activate.failWhileResendingActivationLink'))
        }
      })
    },
    goToSignup () {
      this.$router.push({name: 'Signup'})
    },
    afterUpdate () {
      this.loaded = true
      this.$router.push({name: 'Login'})
    },
    afterError () {
      this.loaded = this.activationError = true
    },
    alreadyActivatedError () {
      // 410 = GONE - activation code is not valid anymore because the user is already activated
      this.showInfo(this.$t('activate.accountAlreadyActivated'))
      this.$router.push({name: 'Login'})
    }
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('activate.pageTitle'))

    if (this.$store.getters.isAuthenticated) {
      this.showInfo(this.$t('activate.youAreAlreadyAuthenticated'))
      this.$router.push({name: 'Home'}) // redirect to home
    } else {
      let crudOptions = {
        queryOnStartup: false,
        skipAutoIndexAfterAllEvents: true,
        updatedMsg: this.$t('activate.accountActivated'),
        skipFormValidation: true,
        409: this.$t('activate.wrongUserIdOrActivationCode'), // CONFLICT - activation code does not belong to specified user id
        404: this.$t('activate.wrongUserId'), // NOT FOUND - user not found by its id
        410: this.alreadyActivatedError // Do not show error message, but run a custom function instead of it
      }
      // Set up the CRUD fr this component
      VueRestCrud.Controller.set(this, activateService, crudOptions)

      // Set the properties that are gonna be used by the CRUD to update/activate the user
      this.resource.userId = this.$route.params.userId
      this.resource.activationCode = this.$route.params.activationCode

      // Update the user, activating its account
      this.update()
    }
  }
}

import unsubscribeService from './unsubscribe-service'
import {CRUD, CRUDData} from '@/core/crud'

export default {
  data: () => ({
    passHidden: true,
    loaded: false,
    activationError: false,
    ...CRUDData // adds: resource, resources, crudReady
  }),
  computed: {
    info () {
      return this.activationError ? this.$t('newsOptOut.wrongUserEmailOrAlreadyUnsubscribed') : this.$t('newsOptOut.unsubscribing')
    }
  },
  methods: {
    goToSignup () {
      this.$router.push({name: 'Signup'})
    },
    afterUpdate () {
      this.loaded = true
      this.$router.push({name: 'Login'})
    },
    afterError () {
      this.loaded = this.activationError = true
    }
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('newsOptOut.pageTitle'))

    let crudOptions = {
      queryOnStartup: false,
      skipAutoIndexAfterAllEvents: true,
      updatedMsg: this.$t('newsOptOut.unsubscribed'),
      skipFormValidation: true,
      409: this.$t('newsOptOut.wrongUserEmailOrAlreadyUnsubscribed'), // CONFLICT - activation code does not belong to specified user id
      404: this.$t('newsOptOut.wrongUserEmailOrAlreadyUnsubscribed'), // NOT FOUND - user not found by its id
      410: this.$t('newsOptOut.wrongUserEmailOrAlreadyUnsubscribed') // Do not show error message, but run a custom function instead of it
    }
    // Set up the CRUD fr this component
    CRUD.set(this, unsubscribeService, crudOptions)

    // Set the properties that are gonna be used by the CRUD to update/activate the user
    this.resource.key = this.$route.params.email
    this.resource.email = this.$route.params.email

    // Update the user, activating its account
    this.update()

  }
}

import unsubscribeService from './unsubscribe-service'
import VueRestClient from 'vue-rest-client'

export default {
  data: () => ({
    loaded: false,
    unsubscribeError: false,
    ...VueRestClient.CrudData // adds: resource, resources, crudReady and modelService
  }),
  computed: {
    info () {
      if (this.loaded) {
        return this.unsubscribeError ? this.$t('unsubscribe.wrongUserEmailOrAlreadyUnsubscribed') : this.$t('unsubscribe.unsubscribed')
      }
      return this.$t('unsubscribe.unsubscribing')
    }
  },
  methods: {
    afterUpdate () {
      this.loaded = true
    },
    afterError () {
      this.loaded = this.unsubscribeError = true
    }
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('unsubscribe.pageTitle'))

    let crudOptions = {
      queryOnStartup: false,
      skipAutoIndexAfterAllEvents: true,
      updatedMsg: this.$t('unsubscribe.unsubscribed'),
      skipFormValidation: true,
      409: this.$t('unsubscribe.wrongUserEmailOrAlreadyUnsubscribed'), // CONFLICT - activation code does not belong to specified user id
      404: this.$t('unsubscribe.wrongUserEmailOrAlreadyUnsubscribed'), // NOT FOUND - user not found by its id
      410: this.$t('unsubscribe.wrongUserEmailOrAlreadyUnsubscribed') // Do not show error message, but run a custom function instead of it
    }
    // Set up the CRUD fr this component
    VueRestClient.Controller.set(this, unsubscribeService, crudOptions)

    // Set the properties that are gonna be used by the CRUD to update/activate the user
    this.resource.code = this.$route.params.code

    // Update the user deactivating the account
    this.update()
  }
}

import userService from '@/shared-services/user-service'
import {CRUD, CRUDData} from '@/core/crud'
import pattern from '@/support/pattern'

// Custom endpoints used to run custom queries in user service
let checkUserNameRegisteredEndpoint = 'wpp/v1/user/username-registered'
let checkEmailRegisteredEndpoint = 'wpp/v1/user/email-registered'

export default {
  created () {
    // extend this component, adding CRUD functionalities and load the tokens
    let options = {
      queryOnStartup: false,
      skipAutoIndexAfterAllEvents: true,
      updatedMsg: this.$t('user.profileUpdated'),
      savedMsg: this.$t('user.accountCreated')
    }
    CRUD.set(this, userService, options)

    if (this.userId) {
      this.mode = 'edit'

      let context = this

      // get the data related to the userId defined
      userService.get(this.userId).then((user) => {
        context.resource = user
        context.usernameValid = true // if we are in edit mode, so the username starts as valid
        context.emailValid = true // if we are in edit mode, so the email starts as valid

        // bind the news letter option
        if (context.resource && context.resource.receive_news) {
          context.receiveNews = context.resource.receive_news
        }

        // As we have set up queryOnStartup as false
        // we have to define manually when the form is ready for crud
        // this is used in conjunction with v-if="crudReady" to avoid
        // render errors while the resource or crud methods are not
        // ready yet. So the form will be rendered only when we say it is ready
        // this property/data is defined in the CRUDData and injected using  ...CRUDData
        context.crudReady = true
      }).catch(error => {
        console.log(error)
        context.showError(this.$t('user.yourProfileDataCouldNotBeLoaded'))
      })
    } else {
      // Set the crud as ready
      this.crudReady = true
    }
  },
  props: {
    userId: {
      required: false
    },
    beforeUpdateFn: {
      type: Function,
      required: false
    },
    afterUpdateFn: {
      type: Function,
      required: false
    },
    submitFn: {
      type: Function,
      required: false
    }
  },
  data () {
    return {
      ...CRUDData, // adds: resource, resources, crudReady
      passVisibility: true,
      sectors: [],
      mode: 'create',
      receiveNews: false,
      useAndDataPolicyAccepted: false,
      usernameValid: null,
      emailValid: null,
      debounceUsernameTimeoutId: null,
      debounceEmailTimeoutId: null
    }
  },
  computed: {
    userNameRules () {
      return [
        (v) => !!v || (this.$t('user.usernameRequired')),
        this.validateUserNameTaken
      ]
    },
    passwordRules () {
      return [
        (this.resource.password === undefined || this.resource.password === this.resource.confirmPassword) || this.$t('user.passwordMatch')
      ]
    },
    emailRules () {
      return [
        v => (!!v && pattern.email.test(v)) || this.$t('user.pleaseTypeAValidEmail'),
        this.validateUserEmailTaken
      ]
    },
    websiteRules () {
      return [
        v => ((v === undefined || v === '') || pattern.websiteUrl.test(v)) || this.$t('user.pleaseTypeAValidWebsite')
      ]
    },
    changePasswordLabel () {
      return this.mode === 'edit' ? this.$t('user.changePasswordOptional') : this.$t('user.setPassword')
    }
  },
  methods: {

    /**
     * Return the icon to be added according the input state
     */
    validatableInputStateIcon (validationStateModel) {
      switch (validationStateModel) {
        case 'checking':
          return 'sync'
        case false:
          return 'warning'
        case true:
          return 'done'
        default:
          return ''
      }
    },

    /**
     * Return the css class to be added to the input according the its state
     */
    validatableInputClass (validationStateModel) {
      switch (validationStateModel) {
        case 'checking':
          return 'checking'
        case false:
          return 'warn'
        case true:
          return 'valid'
        default:
          return ''
      }
    },
    /**
     * When the switch for new letter is changed, set its value to the resource model
     */
    newsChanged () {
      this.resource.receive_news = this.receiveNews
    },

    /**
     * Function to validate if the user name is valid
     * @param {*} v model value
     */
    validateUserNameTaken (v) {
      return (!!v && this.usernameValid !== false) || this.$t('user.alreadyTakenUsername')
    },

    /**
     * Function to validate if the user email is valid
     * @param {*} v model value
     */
    validateUserEmailTaken (v) {
      return (!!v && this.emailValid !== false && pattern.email.test(v)) || this.$t('user.emailNotValidOrTaken')
    },

    /**
     * When the username change in the username input we check via the back-end api if the typed username is available
     * The first step is to change the state to 'checking' while the request is pending.
     * Once we get the response we update the `usernameValid` attribute and
     * call the username component validation function to make suer that the error message is updated
     * because by default the validator is only fired when the user input data into it
     */
    userNameChanged () {
      if (this.resource.username && this.resource.username.length > 0) {
        let context = this
        clearTimeout(this.debounceUsernameTimeoutId)
        this.debounceUsernameTimeoutId = setTimeout(function () {
          // this is a custom request, so we need to set the options
          let options = {verb: 'post', data: { username: context.resource.username }, raw: true}
          let endPoint = checkUserNameRegisteredEndpoint

          // we want to set the username valid attribute as `checking` while the response is not ready
          context.usernameValid = 'checking'
          userService.customQuery(options, endPoint).then(response => {
            context.usernameValid = !response.data.registered

            // call the username input validator to update the message displayed
            context.$refs.username.validate()
          })
        }, 1000)
      }
    },

    /**
     * When the email change in the email input we check via the back-end api if the typed email is not already registered
     * The first step is to change the state to 'checking' while the request is pending.
     * Once we get the response we update the `emailValid` attribute and
     * call the email component validation function to make suer that the error message is updated
     * because by default the validator is only fired when the user input data into it
     */
    emailChanged () {
      if (this.resource.email) {
        let context = this
        clearTimeout(this.debounceEmailTimeoutId)
        this.debounceEmailTimeoutId = setTimeout(function () {
          if (pattern.email.test(this.resource.email)) {
            // this is a custom request, so we need to set the options
            let options = {verb: 'post', data: { email: context.resource.email }, raw: true}
            let endPoint = checkEmailRegisteredEndpoint

            // we want to set the username valid attribute as `checking` while the response is not ready
            context.emailValid = 'checking'
            userService.customQuery(options, endPoint).then(response => {
              context.emailValid = !response.data.registered

              // call the username input validator to update the message displayed
              context.$refs.userEmail.validate()
            })
          } else {
            context.emailValid = false
          }
        }, 1000)
      }
    },

    /**
     * Handle the form submit
     */
    submit () {
      if (this.hasUseAndDataPolicyPage && !this.useAndDataPolicyAccepted) {
        this.showError(this.$t('user.theDataAndPrivacyPolicyMustBeAccepted'))
        return
      }
      // if a custom submit function is defined, run it
      if (this.submitFn) {
        this.submitFn(this)
      } else { // if not, check if it is an update or create and run it
        if (this.userId) {
          this.update()
        } else {
          this.save()
        }
      }
    },
       /**
     * The api provides some user data as a  meta property, but accept them as root object property
     * so, we set these data here, before updating the user
     * This callback is executed by the @core/crud.js before running the update method
     */
    beforeUpdate () {
      if (this.beforeUpdateFn) {
        this.beforeUpdateFn(this.resource)
      } else {
        this.resource.email = this.resource.metas ? this.resource.email : null
        this.resource.first_name = this.resource ? this.resource.first_name : null
        this.resource.last_name = this.resource ? this.resource.last_name : null
      }
    },

    /**
     * Update the current logged in user's displayed name on dashboard after update the user data
     * This callback is executed by the @core/crud.js after running the update method
     * @param {*} data
     */
    afterUpdate (data) {
      if (this.afterUpdateFn) {
        this.afterUpdateFn(this.resource)
      } else {
        if (this.userId) {
          this.$store.commit('userDisplayName', data.first_name)
        }
      }
    },

    /**
     * Reset the new user form after registration
     */
    afterSave () {
      this.$refs.form.reset()
      this.resource = userService.newModelInstance()
      this.receiveNews = false
      this.usernameValid = null
      this.emailValid = null
    },

    /**
     * We want to show a custom error message in the case when the `username` could not be updated because is already used
     *
     * @param {*} errorResponse
     */
    beforeShowError (errorResponse) {
      // we only modify the error message if the error code is 'rest_user_invalid_slug'
      if (errorResponse.data && errorResponse.data.code === 'rest_user_invalid_slug') {
        errorResponse.data.message = this.$t('user.invalidOrAlreadyTakenUsername')
      }
    }
  }
}

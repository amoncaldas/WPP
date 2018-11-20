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

    submit () {
      if (this.$refs.form.validate()) {
        let data = { login: this.usernameOrEmail }
        passwordResetRequestService.customQuery({verb: 'post', data: data}).then(() => {
          this.showSuccess(this.$t('reset.checkYourEmail'), {mode: 'multi-line'})
        },
        (error) => {
          console.log(error)
          this.showError(this.$t('reset.invalidUserNameOrEmail'))
        })
      }
    }
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('reset.pageTitle'))

    if (this.$route.query.login) {
      this.usernameOrEmail = this.$route.query.login
    }
  }
}

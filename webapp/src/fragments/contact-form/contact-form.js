import contactService from './contact-form-service'
import VueRecaptcha from 'vue-recaptcha'
import VueRestClient from 'vue-rest-client'
import pattern from '@/support/pattern'

export default {
  name: 'contact-form',
  created () {
    // extend this component, adding CRUD functionalities and load the tokens
    let options = {
      queryOnStartup: false,
      skipAutoIndexAfterAllEvents: true,
      savedMsg: this.$t('contactForm.msgSent'),
      saveFailedMsg: this.$t('contactForm.sendErrorMsg')
    }
    VueRestClient.Controller.set(this, contactService, options)
  },
  data () {
    return {
      ...VueRestClient.CrudData, // adds: resource, resources, crudReady and modelService
      verifiedCaptcha: false,
      resource: {},
      context: null,
      ready: true
    }
  },
  computed: {
    emailRules () {
      return [ !!this.resource.email && pattern.email.test(this.resource.email) || this.$t('contactForm.pleaseTypeAValidEmail') ]
    }
  },
  methods: {
    sendMsg () {
      this.save()
    },
    submit () {
      this.showInfo(this.$t('contactForm.processingYourMsg'), {timeout: 6000})
      if (this.$store.getters.options.recaptcha_site_key) {
        this.$refs.recaptcha.execute()
      } else {
        this.sendMsg()
      }
    },
    onCaptchaVerified (recaptchaToken) {
      this.showInfo(this.$t('contactForm.processingYourMsg'), {timeout: 6000})
      this.resource.recaptchaToken = recaptchaToken
      const self = this
      self.$refs.recaptcha.reset()
      self.verifiedCaptcha = true
      this.sendMsg()
    },
    onCaptchaExpired () {
      this.$refs.recaptcha.reset()
      this.verifiedCaptcha = false
    }
  },
  mounted () {
    if (this.$store.getters.options.recaptcha_site_key) {
      this.ready = false
      this.loadRecaptcha().then(() => {
        this.ready = true
      })
    }
  },
  components: {
    VueRecaptcha
  }
}

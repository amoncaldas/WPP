import reportErrorService from './report-error-service'
import VueRecaptcha from 'vue-recaptcha'
import VueRestClient from 'vue-rest-client'

export default {
  name: 'report-error',
  props: {
    persistent: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      visible: true,
      active: true,
      ...VueRestClient.CRUDData, // adds: resource, resources, crudReady
      verifiedCaptcha: false,
      resource: {},
      context: null,
      ready: true
    }
  },
  created () {
    // extend this component, adding CRUD functionalities and load the tokens
    let options = {
      queryOnStartup: false,
      skipAutoIndexAfterAllEvents: true,
      resourceSavedMsg: this.$t('reportError.msgSent'),
      failWhileTryingToSaveResourceMsg: this.$t('reportError.sendErrorMsg')
    }
    VueRestClient.Controller.set(this, reportErrorService, options)
    this.resource.url = location.href
  },

  methods: {
    sendMsg () {
      this.save()
    },
    afterSave () {
      this.resource = reportErrorService.newModelInstance()
    },
    submit () {
      this.showInfo(this.$t('contactForm.processingYourMsg'), {
        timeout: 6000
      })
      if (this.$store.getters.options.recaptcha_site_key) {
        this.$refs.recaptcha.execute()
      } else {
        this.sendMsg()
      }
    },
    onCaptchaVerified (recaptchaToken) {
      this.showInfo(this.$t('contactForm.processingYourMsg'), {
        timeout: 6000
      })
      this.resource.recaptchaToken = recaptchaToken
      const self = this
      self.$refs.recaptcha.reset()
      self.verifiedCaptcha = true
      this.sendMsg()
    },
    onCaptchaExpired () {
      this.$refs.recaptcha.reset()
      this.verifiedCaptcha = false
    },
    close () {
      this.$emit('closed')
    }
  },
  components: {
    VueRecaptcha
  },
  mounted () {
    if (this.$store.getters.options.recaptcha_site_key) {
      this.ready = false
      this.loadRecaptcha().then(() => {
        this.ready = true
      })
    }
  }
}

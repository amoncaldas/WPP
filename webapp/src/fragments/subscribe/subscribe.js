import VueRecaptcha from 'vue-recaptcha'
import {CRUD, CRUDData} from '@/core/crud'
import subscribeService from './subscribe-service'
import pattern from '@/support/pattern'

export default {
  data: () => ({
    name: '',
    email: '',
    ...CRUDData, // adds: resource, resources, crudReady
    verifiedCaptcha: false,
    resource: {},
    ready: false,
    locale: null
  }),
  created () {
    this.locale = this.$i18n.locale
    // extend this component, adding CRUD functionalities and load the tokens
    let options = {
      queryOnStartup: false,
      skipAutoIndexAfterAllEvents: true,
      savedMsg: this.$t('subscribe.subscriptionRegistered'),
      saveFailedMsg: this.$t('subscribe.failWhileTryingToSubscribe'),
      409: this.$t('subscribe.thisEmailIsAlreadySubscribed')
    }
    CRUD.set(this, subscribeService, options)
  },
  props: {
    topBorder: {
      default: true,
      type: Boolean
    }
  },
  computed: {
    nameRules () {
      return [
        v => !!v || this.$t('subscribe.nameRequired')
      ]
    },
    emailRules () {
      return [
        v => (!!v && pattern.email.test(v)) || this.$t('subscribe.emailRequired')
      ]
    },
    locales () {
      let locales = this.$store.getters.options.locales
      let supportedLocales = Object.keys(this.$i18n.messages)
      let availableLocales = []
      for (let key in locales) {
        if (locales[key].slug !== 'neutral' && supportedLocales.includes(locales[key])) {
          let title = locales[key].split('-')[0].toUpperCase()
          availableLocales.push({title: title, value: locales[key]})
        }
      }
      return availableLocales
    }
  },
  methods: {
    sendSubscription () {
      this.save()
    },
    submit () {
      this.resource.locale = this.locale
      this.showInfo(this.$t('subscribe.processingSubscription'), {timeout: 6000})
      if (this.$store.getters.options.recaptcha_site_key) {
        this.$refs.recaptcha.execute()
      } else {
        this.sendSubscription()
      }
    },
    onCaptchaVerified (recaptchaToken) {
      this.showInfo(this.$t('subscribe.processingSubscription'), {timeout: 6000})
      this.resource.recaptchaToken = recaptchaToken
      const self = this
      self.$refs.recaptcha.reset()
      self.verifiedCaptcha = true
      this.sendSubscription()
    },
    onCaptchaExpired () {
      this.$refs.recaptcha.reset()
      this.verifiedCaptcha = false
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

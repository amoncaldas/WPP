import * as showToaster from './show-toaster-mixin'
import store from '@/store/store'
import main from '@/main'

const globalMixins = {
  methods: {
    ...showToaster, // mix the show toaster methods here

    confirmDialog (title, text, options) {
      let confirm = options || {}
      confirm.text = text
      confirm.title = title
      let VueInstance = main.getInstance()
      VueInstance.eventBus.$emit('triggerConfirm', confirm)

      return new Promise((resolve, reject) => {
        VueInstance.eventBus.$on('confirmAnswered', (answer) => {
          if (answer) {
            resolve(answer)
          } else {
            reject(answer)
          }
        })
      })
    },
    infoDialog (title, text, options) {
      let info = options || {}
      info.text = text
      info.title = title
      let VueInstance = main.getInstance()
      VueInstance.eventBus.$emit('triggerShowInfo', info)

      return new Promise((resolve, reject) => {
        VueInstance.eventBus.$on('infoOk', () => {
          resolve()
        })
      })
    },
    buildLink (path) {
      let link = path
      if (store.getters.options.router_mode === 'hash') {
        link = `/#${path}`
      }
      return link
    },
    routeToLink (target, targetBlank = false) {
      if (targetBlank) {
        window.open(target, '_blank')
      } else {
        let link = this.buildLink(target)
        this.$router.push({path: link})
      }
    },
    loadRecaptcha () {
      return new Promise((resolve, reject) => {
        let recaptchaScript = document.createElement('script')
        recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit')
        document.head.appendChild(recaptchaScript)

        // We tried to use onreadystatechange, but it doe snot fire
        // Try to find a better solution insted of timeout to detect
        // when the script has been loaded
        setTimeout(() => {
          resolve()
        }, 2000)
      })
    }
  },
  computed: {
    /**
     * Defines if the data and privacy policy must be shown
     */
    hasDataAndPrivacyPolicyPage () {
      let VueInstance = main.getInstance()
      if (!VueInstance) {
        return false
      }
      let url = VueInstance.dataAndPrivacyUrl
      let has = url !== undefined && url !== null
      return has
    },
    /**
     * Gets the data and privacy page policy url
     */
    dataAndPrivacyUrl () {
      let VueInstance = main.getInstance()
      let url = VueInstance.$store.getters.options['data_and_privacy_url_' + VueInstance.$store.getters.locale]
      return this.buildLink(url)
    }
  }
}

export default globalMixins

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
    loadRecaptcha () {
      return new Promise((resolve, reject) => {
        let recaptchaScript = document.createElement('script')
        recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit')
        document.head.appendChild(recaptchaScript)

        // We tried to use onreadystatechange, but it doe snot fire
        // Try to find another better solution
        setTimeout(() => {
          resolve()
        }, 1000)
      })
    }
  }
}

export default globalMixins

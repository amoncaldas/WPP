import * as showToaster from './show-toaster-mixin'
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
    }
  }
}

export default globalMixins

import {VrcForm, CrudData} from 'vue-rest-client'
import pattern from '@/support/pattern'
import HttpClientOptions from '@/common/http-client-options'

export default {
  name: 'contact-form',
  computed: {
    vrcOptions () {
      return {
        queryOnStartup: false,
        skipAutoIndexAfterAllEvents: true,
        resourceSavedMsg: this.$t('contactForm.msgSent'),
        failWhileTryingToSaveResourceMsg: this.$t('contactForm.sendErrorMsg')
      }
    },
    httpOptions () {
      let options = HttpClientOptions
      return options
    },
    emailRules () {
      return [ !!this.resource.email && pattern.email.test(this.resource.email) || this.$t('contactForm.pleaseTypeAValidEmail') ]
    }
  },
  data() {
    return {
      ...CrudData, // adds: resource, resources, crudReady and modelService
    }
  },
  components: {
    VrcForm
  }
}

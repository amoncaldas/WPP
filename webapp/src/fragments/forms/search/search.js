
// https://itnext.io/how-to-use-google-recaptcha-with-vuejs-7756244400da
import userService from '@/shared-services/user-service'
import {CRUD, CRUDData} from '@/core/crud'

export default {
  data () {
    return {
      verifiedCaptcha: false,
      resource: {},
      passVisibility: true,
      context: null
    }
  },
  props: {

  },
  methods: {
    search () {

    },
  },
  created () {
    // Emit the an event catch by root App component
    // telling it to update the page title
    this.eventBus.$emit('titleChanged', this.$t('search.pageTitle'))
  },
  components: {

  }
}

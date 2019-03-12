import Authentication from '@/fragments/authentication/Authentication'
import Signup from '@/fragments/signup/Signup'

export default {
  name: 'login-or-register',
  props: {
    afterLogin: {
      type: Function,
      required: true
    },
    persistent: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      visible: true,
      activeTab: '1',
      active: true
    }
  },

  methods: {
    afterSignup () {
      this.activeTab = '0'
    },
    close () {
      this.visible = false
      this.$emit('closed')
    }
  },
  components: {
    Authentication,
    Signup
  }
}

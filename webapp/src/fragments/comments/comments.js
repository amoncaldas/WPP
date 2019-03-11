import commentService from './comment-service'
import LogintOrRegister from '@/fragments/login-or-register/LoginOrRegister'
import VueRecaptcha from 'vue-recaptcha'
import {CRUD, CRUDData} from '@/core/crud'
import utils from '@/support/utils'

export default {
  name: 'comments',
  created () {
    this.currentPage = this.page
    this.baseEndpoint = commentService.getEndPoint()
    this.loadData()

    // extend this component, adding CRUD functionalities and load the tokens
    let options = {
      queryOnStartup: false,
      skipAutoIndexAfterAllEvents: true,
      savedMsg: this.$t('comments.commentSent')
    }
    CRUD.set(this, commentService, options)
  },
  watch: {
    $route: function () {
      this.post = null
      setTimeout(() => {
        this.loadData()
      }, 100)
    },
    currentPage: function () {
      this.loadData()
    }
  },
  props: {
    postId: {
      required: false
    },
    page: {
      type: Number,
      default: 1
    },
    max: {
      type: Number,
      default: 10
    },
    open: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      ...CRUDData, // adds: resource, resources, crudReady
      comments: [],
      total: null,
      totalPages: null,
      currentPage: null,
      showAuth: false,
      showLogintOrRegister: false,
      verifiedCaptcha: false,
      resource: {},
      context: null,
      baseEndpoint:null
    }
  },
  computed: {
    commenterAvatar () {
      if (this.$store.getters.user && this.$store.getters.user.avatar) {
        return this.$store.getters.user.avatar
      } else {
        return 'https://www.gravatar.com/avatar/HASH'
      }
    }
  },

  methods: {
    sendComment() {
      let endPoint = this.baseEndpoint + '?post=' + this.postId
      this.resource.$setEndpoint(endPoint)
      let content = this
      this.save().then(()=> {
        content.resource = commentService.newModelInstance()
        content.loadData()
      })
    },
    afterLogin () {
      if(this.resource.content && this.resource.content.length > 1) {
        this.submit()
      }
      this.showLogintOrRegister = false
    },
    openAuthenticatio () {
      this.showLogintOrRegister = true
    },
    submit () {
      this.showInfo(this.$t('comments.processingYourComment'), {timeout: 6000})
      if (this.$store.getters.options.recaptcha_site_key) {
        this.$refs.recaptcha.execute()
      } else {
        this.sendComment()
      }
    },
    onCaptchaVerified (recaptchaToken) {
      this.showInfo(this.$t('comments.processingYourComment'), {timeout: 6000})
      this.resource.recaptchaToken = recaptchaToken
      const self = this
      self.$refs.recaptcha.reset()
      self.verifiedCaptcha = true
      this.sendComment()
    },
    onCaptchaExpired () {
      this.$refs.recaptcha.reset()
      this.verifiedCaptcha = false
    },
    getContent (comment) {
      let cleaned = comment.content.rendered.replace(/<link>/gm, '').replace(/<\/link>/gm, '')
      cleaned = cleaned.replace(/<script>/gm, '').replace(/<\/script>/gm, '')
      cleaned = cleaned.replace(/<img>/gm, '').replace(/<\/img>/gm, '')

      return cleaned
    },
    humanizedDate (commentDate) {
      return utils.getFormattedDateTime(commentDate)
    },
    loadData () {
      let context = this

      let filters = {
        post: this.postId,
        page: this.currentPage,
        per_page: this.max,
      }
      commentService.query(filters).then((response) => {
        context.comments = response
        if (response.raw && response.data) {
          context.comments = response.data
          context.total = Number(response.headers['x-wp-total'])
          context.totalPages = Number(response.headers['x-wp-totalpages'])
        }
      }).catch(error => {
        console.log(error)
        context.showError(this.$t('comments.theCommentsCouldNotBeLoaded'))
      })
    }
  },
  components: {
    LogintOrRegister,
    VueRecaptcha
  }
}
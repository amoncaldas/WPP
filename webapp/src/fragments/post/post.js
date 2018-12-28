import postService from '@/shared-services/user-service'

export default {
  created () {
    // extend this component, adding CRUD functionalities and load the tokens
    if (this.postId) {
      let context = this

      // get the data related to the userId defined
      postService.get(this.postId).then((post) => {
        context.post = post
      }).catch(error => {
        console.log(error)
        context.showError(this.$t('post.thePostCouldNotBeLoaded'))
      })
    } else {
      this.post = this.postData
    }
  },
  props: {
    postId: {
      required: false
    },
    postData: {
      required: false
    }
  },
  data () {
    return {
      post: null
    }
  },
  computed: {

  },
  methods: {

  }
}

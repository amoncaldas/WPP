import mediaService from './media-service'

export default {
  created () {
    if (this.mediaId) {
      let context = this

      // get the data related to the userId defined
      mediaService.get(this.mediaId).then((media) => {
        context.mediaPost = media
      }).catch(error => {
        console.log(error)
        context.showError(this.$t('post.thePostCouldNotBeLoaded'))
      })
    } else {
      this.mediaPost = this.media
    }
  },
  props: {
    mediaId: {
      required: false
    },
    media: {
      required: false
    },
    size: {
      default: 'medium_large'
    },
    maxHeight: {
      default: null
    },
    maxHWith: {
      default: null
    },
    contains: {
      default: false
    }
  },
  data () {
    return {
      mediaPost: null
    }
  },
  computed: {
    url () {
      if(this.mediaPost) {
        return this.mediaPost.media_details.sizes[this.size].source_url
      }
      return null
    },
    title () {
      if (this.mediaPost) {
        return this.mediaPost.title.rendered
      }
    }
  },
}

import mediaService from './media-service'

export default {
  created () {
    // extend this component, adding CRUD functionalities and load the tokens
    if (this.mediaId) {
      let context = this

      // get the data related to the userId defined
      mediaService.get(this.mediaId).then((media) => {
        context.media = media
      }).catch(error => {
        console.log(error)
        context.showError(this.$t('post.thePostCouldNotBeLoaded'))
      })
    } else {
      this.post = this.mediaData
    }
  },
  props: {
    mediaId: {
      required: false
    },
    mediaData: {
      required: false
    },
    size: {
      default: 'medium_large'
    }
  },
  data () {
    return {
      media: null
    }
  },
  computed: {
    url () {
      if(this.media) {
        return this.media.media_details.sizes[this.size].source_url
      }
      return null
    },
    title () {
      if (this.media) {
        return this.media.title.rendered
      }
    }
  },
}

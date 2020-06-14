import VueGallery from 'vue-gallery'

export default {
  props: {
    medias: {
      required: true,
      type: Array
    }
  },
  data () {
    return {
      galleryMediaIndex: null,
      options: {
        fullScreen: false,
        youTubeVideoIdProperty: 'youtube',
        youTubePlayerVars: {
          'autoplay': 1,
          'rel': 0
        },
        youTubeClickToPlay: true
      }
    }
  },
  computed: {
    mediasData () {
      let medias = []
      for (let key in this.medias) {
        let media = this.medias[key]
        media.description = media.description || media.title
        // Avoid repeat title as description
        if (media.description === media.title) {
          media.description = ''
        }
        media.poster = media.url
        media.href = media.url

        // Check if the image is a placeholder for a video
        let matchYoutube = this.matchYoutubeUrl(media.caption)
        if (matchYoutube !== false) {
          media.type = 'text/html'
          media.youtube = matchYoutube
          media.href = media.caption
        } else {
          media.type = media.type === 'image' ? 'image/jpeg' : 'text/html'
        }
        medias.push(media)
      }
      return medias

      // In the gallery field, upload an image to be used as a ‘video placeholder’. Then place the video data into the image’s description or caption.
      // That data will be loaded by ACF for the image which you can use the output the video!
    },
    columnsPerPost () {
      if (this.medias.length >= 4) {
        return this.$vuetify.breakpoint.mdAndUp ? 3 : 6
      }
      return 4
    },
    placeHolder () {
      return 'https://via.placeholder.com/1024x800.jpg?text=' + this.$t('gallery.image')
    }
  },
  methods: {
    matchYoutubeUrl (url) {
      var p = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/
      if (url.match(p)) {
        return url.match(p)[1]
      }
      return false
    }
  },
  components: {
    'vue-gallery': VueGallery
  }
}

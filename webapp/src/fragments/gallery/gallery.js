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
      galleryImageIndex: null
    }
  },
  computed: {
    mediaUrls () {
      return this.lodash.map(this.medias, 'url')
    },
    columnsPerPost () {
      if (this.medias.length >= 4) {
        return $vuetify.breakpoint.mdAndUp ? 3 : 6
      }
      return 4
    }
  },
  components: {
    'vue-gallery': VueGallery
  }
}

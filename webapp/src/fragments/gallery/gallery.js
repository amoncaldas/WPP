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
    }
  },
  components: {
    'vue-gallery': VueGallery
  }
}

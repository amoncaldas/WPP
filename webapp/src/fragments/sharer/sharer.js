export default {
  props: {
    path: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    }
  },
  computed: {
    link () {
      let link = this.buildLink(this.path)
      link = location.origin + link
      return link
    },
    whatsappBase () {
      let isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
      if (isMobile) {
        return 'whatsapp://send?text='
      }

      return 'https://web.whatsapp.com/send?text='
    }
  }
}

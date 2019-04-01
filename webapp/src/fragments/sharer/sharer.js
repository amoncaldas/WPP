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
    link() {
      let link = this.buildLink(this.path)
      link = location.origin + link
      return link
    },
    whatsappBase () {
      this.$vuetify.breakpoint.smAndDown ? 'whatsapp://send?text=' : 'web.whatsapp.com/send?text='
    }
  }
}

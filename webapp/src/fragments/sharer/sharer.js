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
    }
  },
  created() {
    // let item = document.querySelector(".sharer-img")
    // item.getSVGDocument().getElementById("svgInternalID").setAttribute("fill", "red")
  },
}

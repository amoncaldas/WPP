export default {
  computed: {
    user () {
      var user = this.$store.getters.user
      return !user ? false : this.$store.getters.user
    }
  }
}

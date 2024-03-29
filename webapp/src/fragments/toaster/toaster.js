export default {
  data () {
    return {
      visible: false,
      snackbarY: 'top',
      snackbarX: 'right',
      snackbarMode: '',
      snackbarTimeout: 6000,
      snackbarText: null,
      snackbarTheme: null,
      render: true
    }
  },
  methods: {
    show (snack) {
      this.snackbarText = snack.message
      this.snackbarTheme = snack.theme

      if (snack.options) {
        this.snackbarY = snack.options.y || this.snackbarY
        this.snackbarX = snack.options.x || this.snackbarX
        this.snackbarMode = snack.options.mode || this.snackbarMode

        if (snack.options.timeout !== undefined && snack.options.timeout !== null) {
          this.snackbarTimeout = snack.options.timeout
        }
      }

      // Restart the timeout if it was already visible
      // after a while so that the computed timeout
      // is read by the component before we run the
      // component setTimeout
      if (this.visible) {
        setTimeout(() => {
          this.$refs.vSnack.setTimeout()
        }, 100)
      } else {
        this.visible = true
      }
    }
  },
  computed: {
    timeout () {
      return this.snackbarTimeout
    }
  },
  created () {
    this.eventBus.$on('showSnack', (snack) => {
      this.show(snack)
    })
  }
}

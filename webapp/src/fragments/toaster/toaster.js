export default {
  data () {
    return {
      snackbar: false,
      snackbarY: 'top',
      snackbarX: 'right',
      snackbarMode: '',
      snackbarTimeout: 6000,
      snackbarText: null,
      snackbarTheme: null
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
        this.snackbarTimeout = snack.options.timeout || this.snackbarTimeout
      }
      this.snackbar = true
    }
  },
  created () {
    this.eventBus.$on('showSnack', (snack) => {
      this.show(snack)
    })
  }
}

import PrettyCodeViewer from '@/fragments/pretty-code-viewer/PrettyCodeViewer'
export default {
  data: () => ({
    infoPromise: null,
    infoTitle: '',
    infoText: '',
    infoOk: '',
    infoTheme: '',
    infoMaxWidth: '',
    show: false,
    textIsMarkdown: false,
    resizable: false,
    code: null, // if an additional json object is provided it will be displayed beyond the infoText
    zIndex: null
  }),
  methods: {
    showDialog (info) {
      this.infoTitle = info.title
      this.infoText = info.text

      this.infoOk = info.ok || this.$t('global.ok')
      this.infoTheme = info.theme || 'primary'
      this.infoMaxWidth = info.maxWidth || '600'
      this.show = true
      this.resizable = info.resizable
      this.textIsMarkdown = info.markdown
      this.code = info.code // if an additional json object is provided it will be displayed beyond the infoText
      this.zIndex = info.zIndex || 1003
    },
    onOk () {
      this.show = false
      this.eventBus.$emit('infoOk', true)
    }
  },
  created () {
    this.eventBus.$on('triggerShowInfo', (info) => {
      this.showDialog(info)
    })
  },
  components: {
    PrettyCodeViewer
  }
}

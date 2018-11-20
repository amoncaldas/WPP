import PrettyCodeViewer from '@/fragments/pretty-code-viewer/PrettyCodeViewer'
import JsonTree from 'ors-vue-json-tree'
export default {
  data: () => ({
    confirmPromise: null,
    confirmTitle: '',
    confirmText: '',
    confirmYes: '',
    confirmNo: '',
    confirmTheme: '',
    confirmMaxWidth: '',
    show: false,
    textIsMarkdown: false,
    boxGuid: null,
    maximized: false,
    code: null, // if an additional json object is provided it will be displayed beyond the infoText
    resizable: false,
    zIndex: null
  }),
  methods: {
    showDialog (confirm) {
      this.confirmTitle = confirm.title
      this.confirmText = confirm.text

      this.confirmYes = confirm.yes || this.$t('global.yes')
      this.confirmNo = confirm.no || this.$t('global.no')
      this.confirmTheme = confirm.theme || 'primary'
      this.confirmMaxWidth = confirm.maxWidth || '600'
      this.show = true
      this.resizable = confirm.resizable
      this.textIsMarkdown = confirm.markdown // if the infoText should b rendered from a markdown
      this.code = confirm.code // if an additional json object is provided it will be displayed beyond the infoText
      this.zIndex = confirm.zIndex || 1003
    },
    onYes () {
      this.show = false
      this.eventBus.$emit('confirmAnswered', true)
    },
    onNo () {
      this.show = false
      this.eventBus.$emit('confirmAnswered', false)
    }
  },
  created () {
    this.eventBus.$on('triggerConfirm', (confirm) => {
      this.showDialog(confirm)
    })
  },
  components: {
    PrettyCodeViewer,
    JsonTree
  }
}

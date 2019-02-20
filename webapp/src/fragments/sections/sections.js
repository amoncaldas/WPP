import Post from '@/fragments/post/Post'

export default {
  created () {
    this.loadSections()
    this.eventBus.$on('localeChanged', () => {
      this.loadSections()
    })
  },
  props: {
    title: {
      required: false
    },
    columnsPerSection: {
      type: Number,
      default: 12
    }
  },
  data () {
    return {
      sections: []
    }
  },
  computed: {
    boxTitle () {
      return this.title || this.$t('sections.title')
    }
  },
  methods: {
    loadSections () {
      let context = this
       let sections = this.lodash.filter(this.$store.getters.sections, (s)=> {
        return s.path !== '/' && context.$store.getters.currentSection.id !== s.id && s.locale === context.$store.getters.locale
       })
       this.sections = sections
    }
  },
  components: {
    Post
  }
}

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
    },
    max: {
      type: Number,
      default: 10
    },
    random: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      sections: [],
      total: null,
      totalPages: null
    }
  },
  computed: {
    boxTitle () {
      return this.title || this.$t('sections.title')
    }
  },
  methods: {
    loadSections () {
       let sections = this.lodash.filter(this.$store.getters.sections, (s)=> {
        return s.path !== '/'
       })
       if (this.max !== -1) {
        sections = sections.slice(0, this.max)
       }
       if (this.random) {
        sections = this.lodash.shuffle(sections)
       }
       this.sections = sections
    }
  },
  components: {
    Post
  }
}

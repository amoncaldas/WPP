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
      default: 3
    },
    random: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      sections: [],
      totalPages: null,
      loaded: false
    }
  },
  computed: {
    boxTitle () {
      return this.title || this.$t('sections.title')
    },
    total () {
      let currentLocale = this.$store.getters.locale
      let total = this.lodash.filter(this.$store.getters.sections, (s) => {
        return s.path !== '/' && (s.locale === currentLocale || s.locale === 'neutral') && !s.extra.not_listed
      })
      return total
    }
  },

  methods: {
    loadSections () {
      let currentLocale = this.$store.getters.locale
      let sections = this.lodash.filter(this.$store.getters.sections, (s) => {
        return s.path !== '/' && (s.locale === currentLocale || s.locale === 'neutral') && !s.extra.not_listed
      })
      if (this.max !== -1) {
        sections = sections.slice(0, this.max)
      }
      if (this.random) {
        sections = this.lodash.shuffle(sections)
      }
      this.sections = sections
      this.loaded = true
    }
  },
  components: {
    Post
  }
}

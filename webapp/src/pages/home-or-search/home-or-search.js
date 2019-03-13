import Home from '@/pages/home/Home'
import Search from '@/pages/search/Search'

export default {
  computed: {
    isHome () {
      return this.$route.query.s === undefined
    }
  },
  components: {
    Search,
    Home
  }
}


import utils from '@/support/utils'

export default {
  props: {
    post: {
      required: true
    },
    mode: {
      type: String,
      default: 'author' // or 'bio'
    }
  },
  computed: {
    hasAuthor () {
      return this.post._embedded && this.post._embedded.author && this.post._embedded.author[0]
    },
    author () {
      return this.post._embedded.author[0].name
    },
    bio () {
      return this.post._embedded.author[0].description
    },

    authorAvatar () {
      if (this.post._embedded.author[0].avatar_urls) {
        return this.post._embedded.author[0].avatar_urls[96] || this.post._embedded.author[0].avatar_urls[48]
      } else {
        return 'https://www.gravatar.com/avatar/HASH' // returns a generic avatar image
      }
    },
    place () {
      if (typeof this.post.extra.has_places && Object.keys(this.post.places).length > 0) {
        let placeKeys = Object.keys(this.post.places)
        let lastKey = placeKeys[placeKeys.length -1]
        let lastPlace = this.post.places[lastKey]
        var parser = document.createElement('a')
        parser.href = lastPlace.link
        return {
          title: lastPlace.title,
          path: parser.pathname
        }
      }
    }
  },
  methods: {
    formatDateTime (date) {
      return utils.getFormattedDateTime(date)
    }
  }
}

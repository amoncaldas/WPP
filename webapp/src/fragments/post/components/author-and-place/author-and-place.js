
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
    authorName () {
      if (this.post.author_member) {
        return this.post.author_member.title
      }
      return this.post._embedded.author[0].name
    },
    bio () {
      if (this.post.author_member) {
        return this.post.author_member.content
      }
      return this.post._embedded.author[0].description
    },

    authorLink () {
      if (this.post.author_member) {
        var parser = document.createElement('a')
        parser.href = this.post.author_member.link
        return this.buildLink(parser.pathname)
      }
    },

    authorAvatar () {
      if (this.post.author_member && this.post.author_member.featured_thumb_url) {
        return this.post.author_member.featured_thumb_url
      }
      if (this.post._embedded.author[0].avatar_urls) {
        return this.post._embedded.author[0].avatar_urls[96] || this.post._embedded.author[0].avatar_urls[48]
      } else {
        return 'https://www.gravatar.com/avatar/HASH' // returns a generic avatar image
      }
    },
    place () {
      if (this.post.extra.has_places && Object.keys(this.post.places).length > 0) {
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
    },
    postDate () {
      let postDate = this.post.extra.custom_post_date || this.post.date
      return postDate
    }
  },
  methods: {
    formatDateTime (date) {
      return utils.getFormattedDate(date)
    }
  }
}

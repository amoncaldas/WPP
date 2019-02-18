import Post from '@/fragments/post/Post'
import Posts from '@/fragments/posts/Posts'
import Section from '@/support/section'

export default {
  components: {
    Post,
    Posts
  },
  data: () => ({
    listPostEndpoints: [],
    currentSection: null
  }),
  created () {
    this.listPostEndpoints = Section.getListingPosts()
    this.currentSection = Section.getCurrentHomeSection()
  }
}

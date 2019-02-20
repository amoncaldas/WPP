import Post from '@/fragments/post/Post'
import Posts from '@/fragments/posts/Posts'
import Sections from '@/fragments/sections/Sections'
import Section from '@/support/section'

export default {
  components: {
    Post,
    Posts,
    Sections
  },
  data: () => ({
    currentSection: null
  }),
  created () {
    this.currentSection = Section.getCurrentHomeSection()
  }
}

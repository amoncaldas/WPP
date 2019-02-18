import ModelService from '@/core/model-service'

let options = {
  raw: true,
  transformResponse: (response) => {
    if (response.data && Array.isArray(response.data)) {
      var parser = document.createElement('a')
      for (let key in response.data) {
        let post = response.data[key]
        parser.href = post.link
        response.data[key].path = `#${parser.pathname}`
      }
    }
  }
}
const postService = new ModelService('wp/v2', 'post', options)

export default postService

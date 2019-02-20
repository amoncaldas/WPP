import ModelService from '@/core/model-service'

let options = {
  raw: true,
  transformResponse: (response) => {
    if (response.data) {
      var parser = document.createElement('a')
      if (Array.isArray(response.data)) {
        for (let key in response.data) {
          let post = response.data[key]
          parser.href = post.link
          response.data[key].path = `${parser.pathname}`
          response.data[key].data = response.data[key].acf
        }
      } else {
        parser.href = response.data.link
        response.data.path = `${parser.pathname}`
        response.data.data = response.data.acf
      }
    }
  }
}
const postService = new ModelService('wp/v2', 'post', options)

export default postService

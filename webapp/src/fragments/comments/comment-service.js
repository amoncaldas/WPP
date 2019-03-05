import ModelService from '@/core/model-service'

let options = {
  raw: true,
  transformResponse: (response) => {
    if (response.data) {
      var parser = document.createElement('a')
      if (Array.isArray(response.data)) {
        for (let key in response.data) {
          let comment = response.data[key]
          parser.href = comment.link
          response.data[key].path = `${parser.pathname}${parser.hash}`
          response.data[key].extra = response.data[key].acf
        }
      } else {
        parser.href = response.data.link
        response.data.path = `${parser.pathname}${parser.hash}`
        response.data.extra = response.data.acf
      }
    }
  }
}
const commentService = new ModelService('wp/v2/comments', 'post', options)

export default commentService

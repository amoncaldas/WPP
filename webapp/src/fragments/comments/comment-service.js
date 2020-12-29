import {ModelService} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: CrudHttpOptions,
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

import {ModelService, CrudHttpApi} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: CrudHttpOptions,
  transformResponse: (response) => {
    if (response.data && Array.isArray(response.data)) {
      var parser = document.createElement('a')
      for (let key in response.data) {
        let section = response.data[key]
        parser.href = section.link
        response.data[key].path = `${parser.pathname}`
      }
    }
  }
}

const sectionService = new ModelService('wp/v2/sections?_embed', 'section', options)

export default sectionService

import {ModelService, CrudHttpApi} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: new CrudHttpApi(CrudHttpOptions)
}
const mediaService = new ModelService('wp/v2/media', 'media', options)

export default mediaService

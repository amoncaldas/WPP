import {ModelService, CrudHttpApi} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: new CrudHttpApi(CrudHttpOptions)
}
const optionService = new ModelService('wpp/v1/services/options', 'option', options)

export default optionService

import {ModelService} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: CrudHttpOptions,
  pk: 'userId'
}
const activationService = new ModelService('wpp/v1/user/activate', 'activation', options)

export default activationService

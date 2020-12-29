import {ModelService} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: CrudHttpOptions,
  pk: 'key'
}
const passwordResetService = new ModelService('wpp/v1/user/password/reset', 'password', options)

export default passwordResetService

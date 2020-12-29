import {ModelService, CrudHttpApi} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: new CrudHttpApi(CrudHttpOptions)
}
const passwordResetRequestService = new ModelService('wpp/v1/user/password/reset/request', 'password', options)

export default passwordResetRequestService

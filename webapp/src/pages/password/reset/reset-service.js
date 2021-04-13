import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions,
  pk: 'key'
}
const passwordResetService = new ModelService('wpp/v1/user/password/reset', 'password', options)

export default passwordResetService

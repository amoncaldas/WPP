import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions
}
const passwordResetRequestService = new ModelService('wpp/v1/user/password/reset/request', 'password', options)

export default passwordResetRequestService

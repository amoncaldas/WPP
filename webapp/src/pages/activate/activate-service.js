import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions,
  pk: 'userId'
}
const activationService = new ModelService('wpp/v1/user/activate', 'activation', options)

export default activationService

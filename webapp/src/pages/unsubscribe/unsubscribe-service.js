import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions,
  pk: 'code'
}
const optOutService = new ModelService('wpp/v1/notifications/unsubscribe', 'Usubscribe', options)

export default optOutService

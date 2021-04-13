import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions,
  pk: 'key'
}
const optOutService = new ModelService('wpp/v1/notifications/subscribe/', 'Subscribe', options)

export default optOutService

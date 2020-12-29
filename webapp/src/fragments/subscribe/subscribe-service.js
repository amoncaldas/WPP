import {ModelService} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: CrudHttpOptions,
  pk: 'key'
}
const optOutService = new ModelService('wpp/v1/notifications/subscribe/', 'Subscribe', options)

export default optOutService

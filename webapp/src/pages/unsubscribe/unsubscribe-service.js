import {ModelService} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: CrudHttpOptions,
  pk: 'code'
}
const optOutService = new ModelService('wpp/v1/notifications/unsubscribe', 'Usubscribe', options)

export default optOutService

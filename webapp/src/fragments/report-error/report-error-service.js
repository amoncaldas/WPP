import {ModelService, CrudHttpApi} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: new CrudHttpApi(CrudHttpOptions)
}
const contactFormService = new ModelService('wpp/v1/message/report-error', 'post', options)

export default contactFormService

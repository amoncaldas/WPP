import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions
}
const contactFormService = new ModelService('wpp/v1/message/send', 'post', options)

export default contactFormService

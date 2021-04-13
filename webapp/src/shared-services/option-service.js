import {ModelService, HttpClient} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions
}
const optionService = new ModelService('wpp/v1/services/options', 'option', options)

export default optionService

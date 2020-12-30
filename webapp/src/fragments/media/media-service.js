import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions
}
const mediaService = new ModelService('wp/v2/media', 'media', options)

export default mediaService

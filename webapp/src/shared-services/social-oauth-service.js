import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions
}

const socialOauthService = new ModelService('wpp/v1/oauth/', 'Social oauth', options)

export default socialOauthService

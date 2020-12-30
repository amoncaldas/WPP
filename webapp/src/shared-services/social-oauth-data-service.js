import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  raw: true,
  httpClientOptions: HttpClientOptions
}

const socialOauthData = new ModelService('wpp/v1/oauth/social-client-data', 'Social oauth', options)

export default socialOauthData

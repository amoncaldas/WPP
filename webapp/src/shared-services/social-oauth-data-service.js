import {ModelService, CrudHttpApi} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: new CrudHttpApi(CrudHttpOptions)
}

const socialOauthData = new ModelService('wpp/v1/oauth/social-client-data', 'Social oauth', options)

export default socialOauthData

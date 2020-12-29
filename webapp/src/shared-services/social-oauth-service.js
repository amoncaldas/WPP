import {ModelService, CrudHttpApi} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  raw: true,
  http: new CrudHttpApi(CrudHttpOptions)
}

const socialOauthService = new ModelService('wpp/v1/oauth/', 'Social oauth', options)

export default socialOauthService

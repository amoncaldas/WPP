import {ModelService} from 'vue-rest-client'
import HttpClientOptions from '@/common/http-client-options'

let options = {
  pk: 'term_id',
  httpClientOptions: HttpClientOptions,
  raw: true
}
const menuService = new ModelService('wp-api-menus/v2/menus', 'menu', options)

export default menuService

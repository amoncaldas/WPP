import {ModelService} from 'vue-rest-crud'
import CrudHttpOptions from '@/common/crud-http-options'

let options = {
  pk: 'term_id',
  http: CrudHttpOptions,
  raw: true
}
const menuService = new ModelService('wp-api-menus/v2/menus', 'menu', options)

export default menuService

import CrudHttpOptions from '@/common/crud-http-options'
import {ModelService} from 'vue-rest-crud'

let options = {
  raw: true,
  http: CrudHttpOptions,
  transformResponse: (response) => {
    // These user's data are brought by default by the wp api
    // but we don't want then in our object
    // because when we update it, if these attributes are present and the
    // user has no admin permission, the api will complain
    // and fail on update the user profile
    if (response.data) {
      delete response.data.roles
      delete response.data.capabilities
      delete response.data.extra_capabilities
      if (response.data.metas) {
        delete response.data.metas.roles
      }
    }
    if (response.config.method === 'get' && response.data.metas) {
      for (let metaKey in response.data.metas) {
        response.data[metaKey] = response.data.metas[metaKey]
      }
    }
  },
  transformRequest: (request) => {
    if (request.running === 'create') {
      request.endPoint = 'wpp/v1/user/register/'
    }
  }
}
const userService = new ModelService('wp/v2/users', 'user', options)

export default userService

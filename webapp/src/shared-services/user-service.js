import ModelService from '@/core/model-service'

let options = {
  pk: 'id',
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
    }
  },
  transformRequest: (request) => {
    if (request.running === 'create') {
      request.endPoint = 'ors-api/v1/user/register/'
    }
  }
}
const userService = new ModelService('wp/v2/users', 'user', options)

export default userService

import ModelService from '@/core/model-service'

let options = {
  raw: true,
  pk: 'login'
}
const passwordResetService = new ModelService('ors-api/v1/user/password/reset', 'password', options)

export default passwordResetService
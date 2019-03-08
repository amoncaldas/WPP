import ModelService from '@/core/model-service'

let options = {
  raw: true,
  pk: 'key'
}
const passwordResetService = new ModelService('wpp/v1/user/password/reset', 'password', options)

export default passwordResetService

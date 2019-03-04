import ModelService from '@/core/model-service'

let options = {
  raw: true
}
const passwordResetRequestService = new ModelService('wpp/v1/user/password/reset/request', 'password', options)

export default passwordResetRequestService

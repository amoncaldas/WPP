import ModelService from '@/core/model-service'

let options = {
  raw: true,
  pk: 'userId'
}
const activationService = new ModelService('ors-api/v1/user/activate', 'activation', options)

export default activationService

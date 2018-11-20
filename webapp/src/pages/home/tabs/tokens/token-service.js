import ModelService from '@/core/model-service'

let options = {pk: 'hash'}
const tokenService = new ModelService('tyk-api/v1/tokens', 'token', options)

export default tokenService

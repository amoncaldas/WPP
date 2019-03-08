import ModelService from '@/core/model-service'

let options = {
  raw: true,
  pk: 'key'
}
const optOutService = new ModelService('wpp/v1//notifications/unsubscribe/', 'Usubscribe', options)

export default optOutService

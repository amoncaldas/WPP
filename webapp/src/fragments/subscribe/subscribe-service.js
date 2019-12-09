import ModelService from '@/core/model-service'

let options = {
  raw: true,
  pk: 'key'
}
const optOutService = new ModelService('wpp/v1/notifications/subscribe/', 'Subscribe', options)

export default optOutService

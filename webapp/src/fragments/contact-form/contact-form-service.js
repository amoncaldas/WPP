import ModelService from '@/core/model-service'

let options = {
  raw: true
}
const contactFormService = new ModelService('wpp/v1/message/send', 'post', options)

export default contactFormService
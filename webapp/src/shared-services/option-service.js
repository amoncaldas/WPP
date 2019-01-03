import ModelService from '@/core/model-service'

let options = {
  raw: true // we dont need each menu resource to be converted to a Model (@/core/model), because it is a read-only resource
}
const optionService = new ModelService('wpp/v1/services/options', 'option', options)

export default optionService

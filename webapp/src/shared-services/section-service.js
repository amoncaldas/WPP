import ModelService from '@/core/model-service'

let options = {
  pk: 'term_id',
  raw: true // we dont need each menu resource to be converted to a Model (@/core/model), because it is a read-only resource
}
const sectionService = new ModelService('wp/v2/sections', 'section', options)

export default sectionService

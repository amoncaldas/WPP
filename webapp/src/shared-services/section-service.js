import ModelServ from '@/core/model-service'

let options = {
  raw: true // we dont need each menu resource to be converted to a Model (@/core/model), because it is a read-only resource
}
const sectionService = new ModelServ('wp/v2/sections?_embed', 'section', options)

export default sectionService

import ModelService from '@/core/model-service'

let options = {
  raw: true
}
const mediaService = new ModelService('wp/v2/media', 'media', options)

export default mediaService

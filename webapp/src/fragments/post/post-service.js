import ModelService from '@/core/model-service'

let options = {
  raw: true
}
const postService = new ModelService('wp/v2', 'post', options)

export default postService

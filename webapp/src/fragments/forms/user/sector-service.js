import ModelService from '@/core/model-service'

let options = {
  raw: true
}
const sectorService = new ModelService('ors-api/v1/sectors', 'sector', options)

export default sectorService

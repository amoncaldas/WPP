import ModelService from '@/core/model-service'

let options = {
  raw: true
}

const socialOauthService = new ModelService('ors-oauth/', 'Social oauth', options)

export default socialOauthService

import ModelService from '@/core/model-service'

let options = {
  raw: true
}

const socialOauthService = new ModelService('wpp/v1/oauth/', 'Social oauth', options)

export default socialOauthService

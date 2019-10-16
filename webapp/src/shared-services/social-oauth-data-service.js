import ModelService from '@/core/model-service'

let options = {
  raw: true
}

const socialOauthData = new ModelService('wpp/v1/oauth/social-client-data', 'Social oauth', options)

export default socialOauthData

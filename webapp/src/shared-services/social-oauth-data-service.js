import ModelService from '@/core/model-service'

let options = {
  raw: true
}

const socialOauthData = new ModelService('ors-oauth/social-client-data', 'Social oauth', options)

export default socialOauthData

import ModelService from '@/core/model-service'
const policyService = new ModelService('tyk-api/v1/available-policies', 'policy', { raw: true })
export default policyService

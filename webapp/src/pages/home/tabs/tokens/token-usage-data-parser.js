import Vue from 'vue'
import theme from '@/common/theme'
import VueInstance from '@/main'

const getLabels = (rawData) => {
  let monthsByIndex = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']
  let labels = []
  Vue.lodash.each(rawData, (data) => {
    let monthName = monthsByIndex[data.id.month - 1]
    let monthTranslation = VueInstance.$t('global.monthsShort.' + monthName)
    labels.push(`${data.id.day}-${monthTranslation}`)
  })
  return labels
}
const getDatasets = (rawData) => {
  let errorDataset = {
    label: 'Error',
    borderColor: theme.error,
    pointBackgroundColor: 'white',
    borderWidth: 1,
    pointBorderColor: theme.error,
    backgroundColor: this.gradient,
    data: []
  }
  let successDataset = {
    label: 'Success',
    borderColor: theme.success,
    pointBackgroundColor: 'white',
    pointBorderColor: theme.success,
    borderWidth: 1,
    backgroundColor: this.gradient2,
    data: []
  }
  Vue.lodash.each(rawData, (data) => {
    errorDataset.data.push(data.error)
    successDataset.data.push(data.success)
  })
  return [errorDataset, successDataset]
}

const parseData = (rawData) => {
  // order the raw data returned from tyk api
  // unfortunately the tyke api does not provide it ordered :-(
  let rawOrdered = Vue.lodash.orderBy(rawData, function (item) {
    // Instantiate a date based in the item year, month and day and use this date to order
    return new Date(item.id.year, item.id.month - 1, item.id.day).getTime()
  })

  let parsed = {
    labels: getLabels(rawOrdered),
    datasets: getDatasets(rawOrdered)
  }
  return parsed
}

export default {
  parseData
}

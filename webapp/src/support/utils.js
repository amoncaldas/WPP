import main from '@/main'
const moment = require('moment')

const utils = {
  /**
   * Get the GET/query url params present in the url as an key value object
   * @param {*} query
   */
  getUrlParams: (query) => {
    if (!query) {
      query = location.search
    }
    if (!query) {
      return {}
    }

    return (/^[?#]/.test(query) ? query.slice(1) : query)
      .split('&')
      .reduce((params, param) => {
        let [key, value] = param.split('=')
        params[key] = value ? decodeURIComponent(value.replace(/\+/g, ' ')) : ''
        return params
      }, {})
  },

  camelCase: (input) => {
    return input.toLowerCase().replace(/_(.)/g, (match, group1) => {
      return group1.toUpperCase()
    })
  },

  slug: (input) => {
    input = input.toLowerCase().replace(/\//g, '_')
    input = input.toLowerCase().replace(/\./g, '_')
    input = input.toLowerCase().replace(/ /g, '_')
    return input
  },

  /**
   * Generates pseudo unique id based in random values and current date time;
   * @return String -  the pseudo unique id
   */
  guid: (prefix) => {
    prefix = prefix ? `${prefix}-` : 'guid-'
    function s4 () {
      return Math.floor((1 + Math.random()) * 0x10000)
        .toString(16)
        .substring(1)
    }
    let random = s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4()
    let dateTime = new Date().getTime()
    return `${prefix}${random}-${dateTime}`
  },

  getValidId: (str) => {
    return str.replace(/\./g, '').replace(/}/g, '').replace(/{/g, '').replace(/\//g, '_')
  },

  getFormattedDate: (value) => {
    let momentDate = moment(value)
    let y = momentDate.year()
    let m = momentDate.month()
    let d = momentDate.date()
    d = d < 10 ? '0' + d.toString() : d
    let instance = main.getInstance()
    let months = instance.$t('global.monthsShort')
    let month = months[Object.keys(months)[m]]
    return `${d} ${month} ${y}`
  },
  getFormattedDateTime: (value) => {
    let dateStr = utils.getFormattedDate(value)
    let dateTime = moment(value)
    let h = dateTime.hour()
    let m = dateTime.minute()
    m = m < 10 ? '0' + m.toString() : m

    return `${dateStr} ${h}:${m}`
  }
}
export default utils

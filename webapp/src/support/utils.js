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
  }
}
export default utils

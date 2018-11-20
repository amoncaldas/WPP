import tokenService from './token-service'
import policyService from './policy-service'
import {CRUD, CRUDData} from '@/core/crud'
import LineChart from '@/fragments/charts/line-chart/line-chart'
import ChartWrapper from '@/fragments/charts/chart-wrapper/ChartWrapper'
import UsageParser from './token-usage-data-parser'
import VueScrollTo from 'vue-scrollto'

export default {
  created () {
    // extend this component, adding CRUD functionalities and load the tokens
    let options = {
      queryOnStartup: true,
      savedMsg: this.$t('home_tokens.savedMsg'),
      confirmDestroyText: this.$t('home_tokens.confirmDestroyText'),
      destroyAbortedMsg: this.$t('home_tokens.destroyAbortedMsg'),
      resourceEmptyMsg: this.$t('home_tokens.tokenEmptyMsg')
    }
    CRUD.set(this, tokenService, options)

    // Get the available policies (type of tokens) and store on the data tokenType's key
    policyService.query().then((policies) => {
      this.tokenTypes = policies
    })
  },
  mounted () {
    let dates = this.getInitialDates()
    this.usageStartDate = dates.from
    this.usageEndDate = dates.to
    this.usageTodayDate = dates.to // initially, `to` is equal today
  },
  data: () => ({
    apiPolicy: null,
    newTokenName: null,
    tokenTypes: [],
    inspectingToken: null,
    usageLabels: [],
    showTokenUsage: false,
    usageDatasets: [],
    usageStartDate: null,
    usageEndDate: null,
    usageTodayDate: null,
    pagination: {
      sortBy: 'token_name'
    },
    showAlert: true,
    ...CRUDData // create the crud data objects (resource, resources and modelService)
  }),
  methods: {
    /**
     * Generate the initial dates to be used as pre selected in the usage date range
     *
     * @returns {from: {Date}, to: {Date}
     */
    getInitialDates () {
      let dates = {}
      let now = new Date()
      let nextWeek = new Date()
      nextWeek.setDate(now.getDate() - 7)
      dates.from = [nextWeek.getFullYear(), this.leadingZero(nextWeek.getMonth() + 1), this.leadingZero(nextWeek.getDate())].join('-')
      dates.to = [now.getFullYear(), this.leadingZero(now.getMonth() + 1), this.leadingZero(now.getDate())].join('-')
      return dates
    },

    leadingZero (nr) {
      return ('0' + nr).slice(-2)
    },
    /**
     * Build the chart with token usage
     *
     */
    buildChart () {
      if (this.showTokenUsage && this.usageStartDate && this.usageEndDate) {
        let usageEndpoint = tokenService.getEndPoint(`${this.inspectingToken.hash}/usage`)
        let filters = {from: this.usageStartDate, to: this.usageEndDate}
        this.showInfo(this.$t('home_tokens.loadingUsageData'), {timeout: 1000})

        tokenService.customQuery({query: filters, raw: true}, usageEndpoint).then((usageRawData) => {
          let parsed = UsageParser.parseData(usageRawData)
          this.usageLabels = parsed.labels
          this.usageDatasets = parsed.datasets
        })
      }
    },
    /**
     * Build a chart with the token usage and then scroll to it
     *
     * @param {*} tokenItem
     */
    showUsage (tokenItem) {
      if (tokenItem.quota.quota_remaining === tokenItem.quota.quota_max) {
        this.showInfo(this.$t('home_tokens.tokenNotUsedYet'))
      } else {
        this.inspectingToken = tokenItem
        this.showTokenUsage = true
        this.buildChart()
        this.scrollToChart()
      }
    },
    /**
     * Scroll to the chart after it is built
     *
     */
    scrollToChart () {
      let options = { easing: 'ease-in', offset: -100, x: false, y: true, container: 'body' }
      // It is necessary to wait a little bit so that the chart is build
      // after the wait we scroll to the chart
      setTimeout(() => {
        VueScrollTo.scrollTo('.chart-box', 2000, options)
      }, 1000)
    },
    /**
     * When the date interval change, rebuild the chart
     *
     */
    onUsageDateRangeChange () {
      this.buildChart()
      // it is not necessary to scroll to the chart
      // because it is already the focus in this case
    },
    /**
     * Change the sort column and resort
     *
     * @param {String} column
     */
    changeSort (column) {
      if (this.pagination.sortBy === column) {
        this.pagination.descending = !this.pagination.descending
      } else {
        this.pagination.sortBy = column
        this.pagination.descending = false
      }
      this.resources = this.lodash.orderBy(this.resources, column, ['desc'])
    },

    /**
     * Copy token key to clipboard
     *
     * @param {*} key
     */
    copyTokenKey (key) {
      if (this.copyToClipboard(key)) {
        this.showSuccess(this.$t('home_tokens.tokenKeyCopiedToClipboard'), {timeout: 2000})
      }
    },
    /**
     * Copy the string to chipboard by creating a temporary textarea element
     *
     * @param {*} str
     * @returns {Boolean}
     */
    copyToClipboard (str) {
      const el = document.createElement('textarea')
      el.value = str
      document.body.appendChild(el)
      el.select()
      let result = document.execCommand('copy')
      document.body.removeChild(el)
      return result
    }
  },
  computed: {
    emptyTableMessage () {
      return this.crudReady ? this.$t('home_tokens.noTokensToList') : this.$t('home_tokens.theTokensAreBeingLoaded')
    }
  },
  components: {
    LineChart,
    ChartWrapper
  }
}

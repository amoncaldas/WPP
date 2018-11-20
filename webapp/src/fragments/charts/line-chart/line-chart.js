import {Line} from 'vue-chartjs'

export default {
  extends: Line,
  data () {
    return {
      gradient: null,
      gradient2: null
    }
  },
  props: {
    labels: {
      type: Array,
      required: true
    },
    datasets: {
      type: Array,
      required: true
    }
  },
  methods: {
    render () {
      this.renderChart(
        {
          labels: this.labels, // this.defaultLabels
          datasets: this.datasets // this.defaultDataSet
        },
        {
          responsive: true,
          maintainAspectRatio: false
        }
      )
    },
    tryRender (event) {
      try {
        this.render(event)
      } catch (error) {
        // silence is gold
      }
    }
  },
  watch: {
    labels: function () {
      this.render()
    },
    datasets: function () {
      this.render()
    }
  },
  mounted () {
    buildDefaultData(this)
    this.render()
    window.addEventListener('resize', this.tryRender)
  },
  destroyed () {
    window.removeEventListener('resize', this.tryRender, false)
  }
}

/**
 * Build default sample chart data
 * @param {*} context
 */
function buildDefaultData (context) {
  context.gradient = context.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450)
  context.gradient2 = context.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450)

  context.gradient2.addColorStop(0, 'rgba(0, 231, 255, 0.9)')
  context.gradient2.addColorStop(0.5, 'rgba(0, 231, 255, 0.25)')
  context.gradient2.addColorStop(1, 'rgba(0, 231, 255, 0)')

  context.gradient.addColorStop(0, 'rgba(255, 0,0, 0.5)')
  context.gradient.addColorStop(0.5, 'rgba(255, 0, 0, 0.25)')
  context.gradient.addColorStop(1, 'rgba(255, 0, 0, 0)')

  context.defaultDataSet = [
    {
      label: 'Error',
      borderColor: '#FC2525',
      pointBackgroundColor: 'white',
      borderWidth: 1,
      pointBorderColor: 'white',
      backgroundColor: context.gradient,
      data: [40, 39, 10, 40, 39, 80, 40]
    },
    {
      label: 'Success',
      borderColor: '#05CBE1',
      pointBackgroundColor: 'white',
      pointBorderColor: 'white',
      borderWidth: 1,
      backgroundColor: context.gradient2,
      data: [60, 55, 32, 10, 2, 12, 53]
    }
  ]

  context.defaultLabels = ['January', 'February', 'March', 'April', 'May', 'June', 'July']
}

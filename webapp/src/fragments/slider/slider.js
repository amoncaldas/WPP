import { VueFlux, FluxPagination, Transitions, FluxCaption, FluxControls, FluxIndex } from 'vue-flux'
export default {
  props: {
    title: {
      required: false
    },
    contents: {
      type: Array,
      default: function () { return [] }
    }
  },
  components: {
    VueFlux,
    FluxPagination,
    FluxCaption,
    FluxControls,
    FluxIndex
  },
  computed: {
    fluxCaptions () {
      let titles = this.lodash.map(this.contents, 'title')
      return titles
    },
    fluxImages () {
      let urls = this.lodash.map(this.contents, 'url')
      return urls
    },
    transition () {
      if (this.$store.getters.options.slider_transition && Transitions[this.$store.getters.options.slider_transition]) {
        return Transitions[this.$store.getters.options.slider_transition]
      }
      let transitions = Transitions
      return transitions.transitionKenburn
    }
  },

  data: () => ({
    fluxOptions: {
      autoplay: true,
      indKeys: true,
      fullscreen: false
    },
    fluxTransitions: {
      transitionBook: Transitions.transitionKenburn
      // Transition options: @see: https://deulos.github.io/vue-flux/
    }
  })
}

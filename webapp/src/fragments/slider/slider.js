import { VueFlux, FluxPagination, Transitions, FluxCaption, FluxControls, FluxIndex } from 'vue-flux';
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
      // see: https://deulos.github.io/vue-flux/
    }
  })
}

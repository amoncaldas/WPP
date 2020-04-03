import * as menuMixin from '@/common/menu-mixin'
export default {
  props: {
    item:
    {
      type: Object,
      required: true
    },
    showIcon: {
      type: Boolean,
      required: false,
      default: false
    },
    showMenuItemFn: {
      type: Function,
      required: false
    },
    navigateFn: {
      type: Function,
      required: false
    }
  },
  methods: {
    ...menuMixin,
    nav (to) {
      if (this.navigateFn) {
        this.navigateFn(to)
      } else {
        this.navigate(to)
      }
    }
  },
  mounted () {
    let context = this
    // When the page title change
    // all the menu must be closed
    this.eventBus.$on('titleChanged', () => {
      if (context.$refs.menuRef) {
        context.$refs.menuRef.isActive = false
      }
    })
  }
}

import Vue from 'vue'
import theme from '@/config/theme'
import '../../node_modules/vuetify/src/stylus/app.styl'

import {
  Vuetify,
  VApp,
  VNavigationDrawer,
  VFooter,
  VList,
  VBtn,
  VMenu,
  VIcon,
  VGrid,
  VToolbar,
  transitions,
  VDivider,
  VExpansionPanel,
  VSubheader,
  VForm,
  VTextField,
  VDialog,
  VCard,
  VJumbotron,
  VSnackbar,
  VSelect,
  VCheckbox,
  VTabs,
  VDataTable,
  VProgressLinear,
  VDatePicker,
  VChip,
  VSwitch,
  VAlert,
  VImg,
  VPagination,
  VTextarea,
  VProgressCircular
} from 'vuetify'

Vue.use(Vuetify, {
  theme: theme,
  components: {
    VApp,
    VNavigationDrawer,
    VFooter,
    VList,
    VBtn,
    VMenu,
    VIcon,
    VGrid,
    VToolbar,
    transitions,
    VDivider,
    VExpansionPanel,
    VSubheader,
    VForm,
    VTextField,
    VDialog,
    VCard,
    VJumbotron,
    VSnackbar,
    VSelect,
    VCheckbox,
    VTabs,
    VDataTable,
    VProgressLinear,
    VDatePicker,
    VChip,
    VSwitch,
    VAlert,
    VImg,
    VPagination,
    VTextarea,
    VProgressCircular
  }
})

export default Vue

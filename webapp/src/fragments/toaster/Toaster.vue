<template>
  <v-snackbar v-if="render" ref="vSnack"
    :color="snackbarTheme"
    :timeout="timeout"
    :top="snackbarY === 'top'"
    :bottom="snackbarY === 'bottom'"
    :right="snackbarX === 'right'"
    :left="snackbarX === 'left'"
    :multi-line="snackbarMode === 'multi-line'"
    :vertical="snackbarMode === 'vertical'"
    :auto-height="true"
    v-model="visible">
      {{ snackbarText }}
    <v-btn flat @click.native="visible = false">{{ $t('global.close') }}</v-btn>
    </v-snackbar>
</template>

<script src="./toaster.js"></script>

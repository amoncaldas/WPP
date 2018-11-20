<template>
  <v-snackbar
    :color="snackbarTheme"
    :timeout="snackbarTimeout"
    :top="snackbarY === 'top'"
    :bottom="snackbarY === 'bottom'"
    :right="snackbarX === 'right'"
    :left="snackbarX === 'left'"
    :multi-line="snackbarMode === 'multi-line'"
    :vertical="snackbarMode === 'vertical'"
    v-model="snackbar">
      {{ snackbarText }}
    <v-btn flat @click.native="snackbar = false">{{ $t('global.close') }}</v-btn>
    </v-snackbar>
</template>

<script src="./toaster.js"></script>

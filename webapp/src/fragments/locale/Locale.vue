<template>
  <div class="locale-changer" v-if="locales.length > 1">
    <v-select class="notranslate"
      v-model="currentLocale" @change="selectChanged"
      item-text="title"
      item-value="value"
      :items="locales"
      :label="$t('locale.language')">
    </v-select>
  </div>
</template>
<script src="./locale.js"></script>
<style scoped src="./locale.css"></style>

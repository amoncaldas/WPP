<template>
  <div class="locale-changer">
    <v-select
      v-model="currentLocale"
      item-text="title"
      item-value="value"
      :items="locales"
      :label="$t('locale.language')">
    </v-select>
  </div>
</template>
<script src="./locale.js"></script>
<style scoped src="./locale.css"></style>

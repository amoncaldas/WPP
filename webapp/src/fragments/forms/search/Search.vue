<template>
  <v-form ref="form" v-if="crudReady" @keyup.native.enter="submit">
    <v-text-field
      :label="$t('search.search')"
      ref="searchInput"
      class="search-box"
      v-model="resource.term"
      @keyup="search"
      append-icon="search">
    </v-text-field>
  </v-form>
</template>

<script src="./search.js"></script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style src="./search.css" scoped></style>

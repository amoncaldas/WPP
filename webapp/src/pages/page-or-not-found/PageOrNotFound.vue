<template>
<v-container grid-list-lg fluid class="page-root page" >
  <v-layout row wrap>
    <v-flex md9 >
      <not-found-component v-if="notFound"></not-found-component>
      <post v-else-if="loaded" :is-page="true" :post-data="post" mode="single"></post>
    </v-flex>
    <v-flex md3 >
      <sections></sections>
    </v-flex>
  </v-layout>
</v-container>
</template>
<script src="./page-or-not-found.js"></script>

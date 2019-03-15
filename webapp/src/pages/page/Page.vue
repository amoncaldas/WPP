<template>
<v-container grid-list-lg fluid class="page-root page" >
  <v-layout row wrap>
    <v-flex md9 >
      <post :is-page="true" :post-name="$route.params.postName" mode="single"></post>
    </v-flex>
    <v-flex md3 >
      <sections :random="true"></sections>
    </v-flex>
  </v-layout>
</v-container>
</template>
<script src="./page.js"></script>

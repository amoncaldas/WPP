<template>
<v-container grid-list-lg fluid class="page-root page" >
  <br>
  <v-layout row wrap>
    <v-flex md9 >
      <post :post-name="$route.params.postName" mode="single"></post>
    </v-flex>
    <v-flex md3>
      <posts></posts>
    </v-flex>
  </v-layout>
</v-container>
</template>
<script src="./page.js"></script>

<template>
<v-container class="page-root" grid-list-lg fluid >
  <v-layout row wrap>
    <v-flex md9 >
      <post :post-id="$route.params.postId" mode="single"></post>
    </v-flex>
    <v-flex md3>
      <sections :random="true"></sections>
    </v-flex>
  </v-layout>
</v-container>
</template>
<script src="./single.js"></script>

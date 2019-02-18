<template>
<v-container grid-list-lg fluid >
  <br>
  <v-layout row wrap>
    <v-flex md9 >
      <post :post-id="$route.params.postId" mode="single"></post>
    </v-flex>
    <v-flex md3>
      <posts :key="postType.endpoint" v-for="postType in listPostEndpoints" :endpoint="postType.endpoint" :exclude="[$route.params.postId]" :title="postType.title"></posts>
    </v-flex>
  </v-layout>
</v-container>
</template>
<script src="./single.js"></script>

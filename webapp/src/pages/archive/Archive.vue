<template>
  <v-container fluid class="page-root archive">
    <br>
    <v-slide-y-transition mode="out-in">
      <posts :columns-per-post="6" :endpoint="postType" :title="title"></posts>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./archive.js"></script>

<style src="./archive.css"></style>

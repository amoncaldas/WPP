<template>
  <v-container fluid class="page-root home">
    <v-slide-y-transition mode="out-in">
      <v-layout child-flex>
        <v-flex xs6>
        </v-flex>
        <v-flex xs6>
          <sections-map></sections-map>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./home.js"></script>

<style src="./home.css"></style>

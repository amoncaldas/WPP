<template>
  <v-container fluid class="page-root archive">
    <br>
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex md9>
          <not-found-component v-if="notFound"></not-found-component>
          <posts :page="page" :max="12" :parent-id="parentSectionId" :columns-per-post="$vuetify.breakpoint.mdAndUp ? 6: 12" :endpoint="postType"
            :title="title"></posts>
        </v-flex>
        <v-flex md3>
          <sections :random="true"></sections>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./archive.js"></script>

<style src="./archive.css"></style>

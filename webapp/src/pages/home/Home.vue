<template>
  <v-container fluid class="page-root home">
    <slider v-if="currentSection.extra.has_image_slides" :contents="currentSection.extra.slide_images"></slider>
    <div class="content" v-if="currentSection.extra.has_content" v-html="currentSection.extra.html_content"></div>
    <sections-map></sections-map>
    <br><br>
    <template v-if="currentSection.extra.list_posts">
      <posts :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint" v-for="postType in listingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
    </template>
  </v-container>
</template>

<script src="./home.js"></script>

<style src="./home.css"></style>

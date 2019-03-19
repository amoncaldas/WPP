<template>
  <v-container fluid class="page-root home">
    <slider v-if="currentSection.extra.has_image_slides" :contents="currentSection.extra.slide_images"></slider>
    <div class="content" v-if="currentSection.extra.has_content" v-html="currentSection.extra.html_content"></div>
    <template>
      <br>
      <sections-map v-if="currentSection && currentSection.extra.has_section_map"></sections-map>
      <br>
    </template>
    <template v-if="currentSection && currentSection.extra.has_places">
      <br>
      <post-map :post="currentSection"></post-map>
      <br>
    </template>
    <template v-if="currentSection.extra.compact_list_posts">
      <br>
      <posts mode="compact" :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint + '_compact'" v-for="postType in compactListingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
      <br>
    </template>
    <template v-if="currentSection.extra.list_posts">
      <posts :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint" v-for="postType in listingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
    </template>
  </v-container>
</template>

<script src="./home.js"></script>

<style src="./home.css"></style>

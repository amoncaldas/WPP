<template>
  <v-container fluid class="page-root section">
    <slider v-if="currentSection.extra.has_image_slides" :contents="currentSection.extra.slide_images"></slider>
    <br>
    <template  v-if="currentSection.extra.has_content">
      <div class="content" v-html="currentSection.extra.html_content"></div>
      <br>
    </template>
    <post-map @placeClicked="placeClicked" v-if="currentSection && currentSection.extra.has_places" :post="currentSection"></post-map>
    <br>
    <template v-if="currentSection.extra.compact_list_posts">
      <posts :max="maxCompact" mode="compact" :parent-id="currentSection.id" :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint" v-for="postType in compactListingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
    </template>
    <div v-if="currentSection.extra.list_posts">
      <template v-for="postType in listingPosts" >
        <template v-if="postType.endpoint === 'sections'">
          <sections :key="postType.endpoint" :max="max" :columns-per-section="$vuetify.breakpoint.mdAndUp ? 4 : 6" :random="true"></sections>
          <br :key="postType.endpoint + 'br'">
        </template>
        <template v-else>
          <posts :max="max" :parent-id="currentSection.id" :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </template>
      </template>
    </div>
  </v-container>
</template>

<script src="./section.js"></script>

<style src="./section.css"></style>

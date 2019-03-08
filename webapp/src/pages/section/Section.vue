<template>
  <v-container fluid class="page-root section">
    <v-slide-y-transition mode="out-in">
      <div>
        <slider v-if="currentSection.extra.has_image_slides" :contents="currentSection.extra.slide_images"></slider>
        <div v-if="currentSection.extra.has_content" v-html="currentSection.html_content"></div>
        <br>
        <post-map v-if="currentSection && currentSection.extra.has_places" :post="currentSection"></post-map>
        <br>
        <div v-if="currentSection.extra.list_posts">
          <posts :parent-id="currentSection.id" :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint" v-for="postType in listingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </div>
      </div>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./section.js"></script>

<style src="./section.css"></style>

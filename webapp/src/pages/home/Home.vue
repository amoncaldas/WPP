<template>
  <v-container fluid class="page-root home">
    <v-slide-y-transition mode="out-in">
      <div>
        <br>
        <slider v-if="currentSection.data.has_image_slides" :contents="currentSection.data.slide_images"></slider>
        <div v-if="currentSection.data.has_content" v-html="currentSection.html_content"></div>
        <br><br>
        <sections-map></sections-map>
        <br><br>
        <div v-if="currentSection.data.list_posts">
          <posts :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint" v-for="postType in listingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </div>
      </div>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./home.js"></script>

<style src="./home.css"></style>

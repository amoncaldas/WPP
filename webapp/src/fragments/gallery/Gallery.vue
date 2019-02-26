<template>
  <div>
    <vue-gallery :images="mediaUrls" :index="galleryImageIndex" @close="galleryImageIndex = null"></vue-gallery>
    <v-container style="padding:5px" grid-list-lg fluid >
      <v-layout row wrap>
        <v-flex  v-for="(image, imageIndex) in medias" v-bind="{['xs'+ $vuetify.breakpoint.mdAndUp ? 4 : 6]: true}" :key="imageIndex">
          <div class="image" :key="imageIndex" @click="galleryImageIndex = imageIndex"    :style="{ backgroundImage: 'url(' + image.url + ')', width: '300px', height: '200px' }" ></div>
        </v-flex>
      </v-layout>
    </v-container>
  </div>
</template>
<script src="./gallery.js"></script>
<style scoped src="./gallery.css"></style>

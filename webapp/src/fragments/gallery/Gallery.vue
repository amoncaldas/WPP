<template>
  <div>
    <vue-gallery :images="mediaUrls" :index="galleryImageIndex" @close="galleryImageIndex = null"></vue-gallery>
    <v-container style="padding:5px" grid-list-lg fluid >
      <v-layout row wrap>
        <template v-for="(image, imageIndex) in medias">
          <v-flex v-bind="{['xs'+ columnsPerPost]: true}" :key="imageIndex">
            <div class="image" :key="imageIndex" @click="galleryImageIndex = imageIndex"    :style="{ backgroundImage: 'url(' + image.url + ')', height: '200px' }" ></div>
          </v-flex>
        </template>
      </v-layout>
    </v-container>
  </div>
</template>
<script src="./gallery.js"></script>
<style scoped src="./gallery.css"></style>

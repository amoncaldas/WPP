<template>
  <div>
    <vue-gallery
      :images="mediasData"
      :index="galleryMediaIndex"
      :options="options"
      @close="galleryMediaIndex = null">
      <v-icon color="primary" large :title="$t('gallery.close')" slot="close">close</v-icon>
    </vue-gallery>
    <v-container style="padding:5px" grid-list-lg fluid >
      <v-layout row wrap>
        <template v-for="(media, mediaIndex) in mediasData">
          <v-flex v-bind="{['xs'+ columnsPerPost]: true}" :key="mediaIndex">
            <!-- <div class="image" :key="imageIndex" @click="galleryImageIndex = imageIndex"    :style="{ backgroundImage: 'url(' + image.url + ')', height: '200px' }" ></div> -->
            <v-img v-if="media.poster"
              :src="media.url"
              :key="mediaIndex"
              @click="galleryMediaIndex = mediaIndex"
              position="top center"
              :max-height="200"
              :contains="true"
              :lazy-src="placeHolder"
              style="max-width:100%"
              :alt="media.description">
            </v-img>
          </v-flex>
        </template>
      </v-layout>
    </v-container>
  </div>
</template>
<script src="./gallery.js"></script>
<style scoped src="./gallery.css"></style>

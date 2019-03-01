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
            <div class="image-container">
              <img :src="media.url" :alt="media.title" :title="media.description"  @click="galleryMediaIndex = mediaIndex" class="image">
            </div>
          </v-flex>
        </template>
      </v-layout>
    </v-container>
  </div>
</template>
<script src="./gallery.js"></script>
<style scoped src="./gallery.css"></style>

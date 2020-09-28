<template>
  <v-container v-if="currentSection && loaded" fluid class="page-root home">
    <template v-if="currentSection.extra.has_highlighted_top">
      <highlighted position="top" :columns-per-post="6" :content-id="currentSection.id"> </highlighted>
    </template>
    <slider v-if="currentSection.extra.has_image_slides" :contents="currentSection.extra.slide_images"></slider>
    <template v-if="currentSection.extra.has_highlighted_middle">
      <br/>
      <highlighted position="middle" :columns-per-post="6" :content-id="currentSection.id"> </highlighted>
    </template>
    <div class="content" v-if="currentSection.extra.has_content" v-html="currentSection.extra.html_content"></div>
    <template v-if="hasMaps">
      <wpp-map v-for="mapId in currentSection.extra.maps" :key="mapId" @placeClicked="placeClicked" :height-unit="currentSection.extra.map_height_unit" :height="currentSection.extra.map_height" :map-id="mapId"></wpp-map>
      <br/>
    </template>
    <template v-if="currentSection.extra.compact_list_posts">
      <br>
      <posts :max="maxCompact" mode="compact" :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint + '_compact'" v-for="postType in compactListingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
      <br>
    </template>
    <template v-if="currentSection.extra.list_posts">
      <template v-for="postType in listingPosts">
        <template v-if="postType.endpoint === 'sections'">
          <sections :key="postType.endpoint" :max="max" :columns-per-section="$vuetify.breakpoint.mdAndUp ? 4 : 6"></sections>
          <br :key="postType.endpoint + 'br'">
        </template>
        <template v-else>
          <posts :max="max" :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :key="postType.endpoint" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </template>
      </template>
    </template>
    <template v-if="currentSection.extra.has_highlighted_bottom">
      <br/>
      <highlighted position="bottom" :columns-per-post="6" :content-id="currentSection.id"> </highlighted>
    </template>
  </v-container>
</template>

<script src="./home.js"></script>

<style src="./home.css"></style>

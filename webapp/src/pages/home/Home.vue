<template>
  <v-container v-if="currentSection" fluid class="page-root home">
    <template v-if="currentSection.extra.has_highlighted_top">
      <highlighted position="top" :columns-per-post="6" :content-id="currentSection.id"> </highlighted>
    </template>
    <slider v-if="currentSection.extra.has_image_slides" :contents="currentSection.extra.slide_images"></slider>
    <template v-if="currentSection.extra.has_highlighted_middle">
      <br/>
      <highlighted position="middle" :columns-per-post="6" :content-id="currentSection.id"> </highlighted>
    </template>
    <div class="content" v-if="currentSection.extra.has_content" v-html="currentSection.extra.html_content"></div>
    <template>
      <br>
      <sections-map v-if="currentSection && currentSection.extra.has_section_map"></sections-map>
      <br>
    </template>
    <template v-if="currentSection && currentSection.extra.has_places">
      <br>
      <post-map @placeClicked="placeClicked" :post="currentSection"></post-map>
      <br>
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

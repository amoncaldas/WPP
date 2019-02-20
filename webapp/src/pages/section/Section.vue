<template>
  <v-container fluid class="page-root section">
    <v-slide-y-transition mode="out-in">
      <div>
        <div v-if="currentSection.data.has_content" v-html="currentSection.content"></div>
        <br>
        <post-map v-if="currentSection && currentSection.data.has_places" :post="currentSection"></post-map>
        <br>
        <div v-if="currentSection.data.list_posts">
          <posts :columns-per-post="6" :key="postType.endpoint" v-for="postType in listingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </div>
      </div>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./section.js"></script>

<style src="./section.css"></style>

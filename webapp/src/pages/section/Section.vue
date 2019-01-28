<template>
  <v-container fluid class="page-root section">
    <v-slide-y-transition mode="out-in">
      <div>
        <div v-if="currentSection.acf.has_content" v-html="currentSection.content"></div>
        <br><br>
        <post-map v-if="currentSection && currentSection.acf.has_places" :post="currentSection"></post-map>
        <br><br>
        <div v-if="currentSection.acf.list_posts">
          <posts  :columns-per-post="6" :key="postType.endpoint" v-for="postType in listPostEndpoints" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </div>
      </div>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./section.js"></script>

<style src="./section.css"></style>

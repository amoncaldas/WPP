<template>
  <v-container fluid class="page-root home">
    <v-slide-y-transition mode="out-in">
      <div>
        <div v-if="currentSection.data.has_content" v-html="currentSection.content"></div>
        <br><br>
        <sections-map></sections-map>
        <br><br>
        <div v-if="currentSection.data.list_posts">
          <posts :columns-per-post="6" :key="postType.endpoint" v-for="postType in listingPosts" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </div>
      </div>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./home.js"></script>

<style src="./home.css"></style>

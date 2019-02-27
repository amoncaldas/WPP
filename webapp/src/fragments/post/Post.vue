<template>
<div>
  <box class="post" background="white" :no-top-border="noTopBorder" v-if="post">
    <div slot="header">
      <a v-bind:href="post.path" :style="{color:theme.dark}" v-if="mode === 'list'" style="margin-left:0px; text-decoration:none" ><h3>{{title}}</h3></a>
      <h3 v-else>{{title}}</h3>
    </div>
    <div slot="content">
      <media v-if="post._embedded" :media="featuredMedia" :max-height="mode === 'single'? 500 : 200"></media>
      <media v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="mode === 'single'? 500 : 200"></media>
      <br>
      <template v-if="mode === 'single'">
        <div v-html="content"></div>
        <post-map v-if="post.data && post.data.has_places" :post="post"></post-map>
        <br>
        <box v-if="post.data.medias" background="white" :no-top-border="noTopBorder">
          <div slot="header">Gallery</div>
          <gallery :medias="post.data.medias" ></gallery>
        </box>
      </template>
      <template v-else>
        <div class="post-locale" :title="$t('post.contentLanguage')" :style="{'border-bottom-color': theme.accent}"> <v-icon>language</v-icon><span> {{post.locale | uppercase}}</span></div>
        <p>{{excerpt}}</p>
        <v-btn style="margin-left:0px" :href="'/#'+post.path" :title="$t('post.readMore')" flat>{{ $t('post.readMore')}}</v-btn>
      </template>
    </div>
  </box>
  <template v-if="post && mode === 'single'">
    <br><br>
    <related :columns-per-post="4" :content-id="post.id" :max="6"> </related>
  </template>
</div>
</template>
<style scoped src="./post.css"></style>
<script src="./post.js"></script>
<style scoped src="./post.css"></style>

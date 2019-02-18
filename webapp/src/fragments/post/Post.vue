<template>
  <box background="white" :no-top-border="noTopBorder" v-if="post">
    <div slot="header">
      <a v-bind:href="post.path" :style="{color:theme.dark}" v-if="mode === 'list'" style="margin-left:0px; text-decoration:none" ><h3>{{post.title.rendered}}</h3></a>
      <h3 v-else>{{post.title.rendered}}</h3>
    </div>
    <div slot="content">
      <media v-if="post._embedded" :media="featuredMedia" :max-height="500" :contains="true"></media>
      <media v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="500" :contains="true"></media>
      <br>
      <p>
        {{excerpt()}}
      </p>
      <v-btn v-if="mode === 'list'" style="margin-left:0px" :href="post.path" flat>{{ $t('post.readMore')}}</v-btn>
    </div>
  </box>
</template>
<script src="./post.js"></script>

<template>
  <box background="white" :no-top-border="noTopBorder" v-if="post">
    <div slot="header">
      <h3>{{post.title.rendered}}</h3>
    </div>
    <div slot="content">
      <media v-if="post._embedded" :media="featuredMedia"></media>
      <media v-else-if="post.featured_media" :media-id="post.featured_media"></media>
      <p>
        {{excerpt()}}
      </p>
      <v-btn v-if="mode === 'list'" style="margin-left:0px" :href="post.url" flat>{{ $t('post.readMore')}}</v-btn>
    </div>
  </box>
</template>
<script src="./post.js"></script>

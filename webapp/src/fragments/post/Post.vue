<template>
  <box background="white" :no-top-border="noTopBorder">
    <div slot="header">
      <h3>{{post.title.rendered}}</h3>
    </div>
    <div slot="content">
      <media v-if="post.featured_media" :media-id="post.featured_media"></media>
      <p>
        {{excerpt()}}
      </p>
      <v-btn flat :href="post.link">{{ $t('post.readMore')}}</v-btn>
    </div>
  </box>
</template>
<script src="./post.js"></script>

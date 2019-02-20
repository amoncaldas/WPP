<template>
<div>
  <box background="white" :no-top-border="noTopBorder" v-if="post">
    <div slot="header">
      <a v-bind:href="post.path" :style="{color:theme.dark}" v-if="mode === 'list'" style="margin-left:0px; text-decoration:none" ><h3>{{post.title.rendered}}</h3></a>
      <h3 v-else>{{post.title.rendered}}</h3>
    </div>
    <div slot="content">
      <media v-if="post._embedded" :media="featuredMedia" :max-height="mode === 'single'? 500 : 200"></media>
      <media v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="mode === 'single'? 500 : 200"></media>
      <br>
      <template v-if="mode === 'single'">
        <div v-html="content"></div>
        <post-map v-if="post.data && post.data.has_places" :post="post"></post-map>
      </template>
      <template v-else>
        <div v-if="explicitLocale"> <v-icon>language</v-icon><span> {{post.locale | uppercase}}</span></div>
        <p>{{excerpt}}</p>
        <v-btn style="margin-left:0px" :href="'/#'+post.path" :title="$t('post.readMore')" flat>{{ $t('post.readMore')}}</v-btn>
      </template>
    </div>
  </box>
  <br>
  <br>
  <posts v-if="post && mode === 'single'"
    :columns-per-post="4" :exclude="[post.id]"
    :endpoint="$store.getters.postTypeEndpoint"
    :include="related"
    :title="$t('post.related')">
  </posts>
</div>
</template>
<script src="./post.js"></script>

<template>
  <div class="authoring-container" v-if="hasAuthor">
    <v-layout row wrap>
      <v-flex xs3 sm2 md1 style="padding-right:5px; padding-left:5px">
        <div class="author-avatar" :class="{'only-author': mode === 'author'}">
          <img :src="authorAvatar" :alt="authorName" :title="authorName">
        </div>
      </v-flex>
      <v-flex xs9 sm10 md11>
        <div>
          <template v-if="authorLink" class="notranslate">
            <span> {{$t('author.by')}}</span> <a :title="authorName" :href="authorLink"><b>{{authorName}}</b></a>
          </template>
          <span v-else> {{$t('author.by')}} <b>{{authorName}}</b></span>
        </div>
        <div>
          <span v-if="mode === 'author'">
            {{$t('author.on') | capitalize}} <time :datetime="postDate">{{formatDateTime(postDate)}}</time>
          </span>
          <span v-else>
            {{$t('author.authorProfile')}}
          </span>
        </div>
        <div v-if="mode === 'author' && place && !this.post.extra.no_authoring_place">
          <v-icon class="notranslate">place</v-icon>
          <a :href="buildLink(place.path)" :title="place.title" flat ><b>{{place.title}}</b></a>
        </div>
      </v-flex>
    </v-layout>
    <div v-if="mode === 'bio'" v-html="bio">
    </div>
  </div>
</template>


<script src="./author-and-place.js"></script>
<style scoped src="./author-and-place.css"></style>

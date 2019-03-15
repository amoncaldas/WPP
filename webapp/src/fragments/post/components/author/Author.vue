<template>
  <div class="authoring-container" v-if="hasAuthor">
    <v-layout row wrap>
      <v-flex xs2 sm1 style="padding-right:10px">
        <div class="author-avatar">
          <img :src="authorAvatar" :alt="author" :title="author">
        </div>
      </v-flex>
      <v-flex xs10 sm11 >
        <div>
          <span> {{$t('author.by')}} <b>{{author}}</b></span>
        </div>
        <div>
          <span v-if="mode === 'author'">
            {{$t('author.on') | capitalize}} <time :datetime="post.date">{{formatDateTime(post.date)}}</time>
          </span>
          <span v-else>
            {{$t('author.authorProfile')}}
          </span>
        </div>
        <div v-if="mode === 'author' && place">
          <v-icon>place</v-icon>
          <a :href="buildLink(place.path)" :title="place.title" flat ><b>{{place.title}}</b></a>
        </div>
      </v-flex>
    </v-layout>
    <div v-if="mode === 'bio'">
      {{bio}}
    </div>
  </div>
</template>


<script src="./author.js"></script>
<style scoped src="./author.css"></style>

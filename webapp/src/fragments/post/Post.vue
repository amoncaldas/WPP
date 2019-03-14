<template>
<div>
  <box :tag="mode === 'single' ? 'main' : 'article'" class="post" background="white" :no-top-border="noTopBorder" v-if="post">
    <div slot="header" v-if="mode !== 'compact'" >
      <template v-if="titleWithLink">
        <a v-bind:href="buildLink(link)" :title="title" :style="{color:$vuetify.theme.dark}" style="margin-left:0px; text-decoration:none" >
          <h1 v-if="mode === 'single'">{{title}}</h1>
          <h3 v-else>{{title}}</h3>
        </a>
      </template>
      <template v-else>
        <h1 v-if="mode === 'single'">{{title}}</h1>
        <h3 v-else>{{title}}</h3>
      </template>
    </div>
    <div slot="content">
      <template v-if="mode !== 'compact' && mode !== 'list'">
        <media :mode="mode" v-if="post._embedded" :media="featuredMedia" :max-height="mode === 'single'? 500 : 200"></media>
        <media :mode="mode" v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="mode === 'single'? 500 : 200"></media>
        <br>
      </template>
      <template v-if="mode === 'single'">
        <div class="authoring-container">
          <span>
            <v-alert :color="$vuetify.theme.dark" :value="true" icon="edit" outline type="info" >
              <span v-if="!renderAsPage"> {{$t('post.by')}} <b>{{author}}</b> </span> {{$t('post.on')}} <time :datetime="post.date">{{formatDateTime(post.date)}}</time>
            </v-alert>
          </span>
          </div>
        <div class="html-content" v-html="content"></div>
        <div class="availability">
          <v-chip v-if="post.extra.available" color="secondary" dark :title="$t('post.available')" >{{ $t('post.available') | capitalize}}</v-chip>
          <span v-else-if="post.extra.available_at">
            <b> {{$t('post.availableAt')}} </b>
            <v-chip color="secondary" dark :title="$t('post.available_at')" >{{formatDate(post.extra.available_at)}}</v-chip>
          </span>
        </div>
        <div class="cat-and-tgs">
          <template v-if="categories.length > 0">
            <h4>{{$t('post.categories') | capitalize}}</h4>
            <v-chip color="secondary" :title="category.name" dark :key="index + '_cat'" v-for="(category, index) in categories">{{category.name}}</v-chip>
          </template>
          <template v-if="tags.length > 0">
            <h4>{{$t('post.tags') | capitalize}}</h4>
            <v-chip color="secondary" :title="tag.name" dark :key="index + '_tag'" v-for="(tag, index) in tags">{{tag.name}}</v-chip>
          </template>
        </div>
        <post-map v-if="post.extra && post.extra.has_places" @placeClicked="placeClicked" :post="post"></post-map>
        <box v-if="post.extra.medias" background="white" :no-top-border="noTopBorder">
          <div slot="header">{{$t('post.gallery')}}</div>
          <div slot="content">
            <br>
            <gallery :medias="post.extra.medias" ></gallery>
          </div>
        </box>
      </template>
      <template v-else>
        <div v-if="post.locale !== 'neutral' && post.locale !== $store.getters.locale" class="post-locale" :title="$t('post.contentLanguage')" :style="{'border-bottom-color': $vuetify.theme.accent}"> <v-icon>language</v-icon><span> {{post.locale | uppercase}}</span></div>
        <div v-if="post.extra && post.extra.is_sponsored" class="post-sponsored" :title="$t('post.sponsored')" :style="{'border-bottom-color': $vuetify.theme.accent}"> <i>{{$t('post.sponsored')}}</i></div>

        <v-layout row wrap v-if="mode === 'list'">
          <v-flex sm3 style="padding-right:10px">
            <media :mode="mode" v-if="featuredMedia" :media="featuredMedia" :max-height="200"></media>
            <media :mode="mode" v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="200"></media>
          </v-flex>
          <v-flex sm9 >
            <div v-if="excerpt && excerpt.length > 0">{{excerpt}}</div>
          </v-flex>
        </v-layout>
        <template v-else>
          <div v-if="excerpt && excerpt.length > 0">{{excerpt}}</div>
        </template>
        <span v-if="showType">
          <v-chip :title="$t('post.contentType')" >{{type | capitalize}}</v-chip>
        </span>
        <div style="text-align:right" :style="{float:showType ? 'right' : 'none'}">
          <v-btn style="margin-left:0px" :href="buildLink(link)" :title="$t('post.readMore')" flat ><b>{{ $t('post.readMore')}}</b></v-btn>
        </div>
      </template>
    </div>
  </box>
  <template v-if="post && mode === 'single'">
    <br><br>
    <v-tabs class="post-tabs" slider-color="primary">
      <v-tab ripple>
        {{ $t('post.related') }}
      </v-tab>
      <v-tab ripple>
        {{$t('post.comments')}}
      </v-tab>
      <v-tab-item>
        <related :columns-per-post="4" :content-id="post.id" :max="6"> </related>
      </v-tab-item>
      <v-tab-item>
        <comments :open="post.comment_status === 'open'" :post-id="post.id"></comments>
      </v-tab-item>
    </v-tabs>

  </template>
</div>
</template>

<script src="./post.js"></script>
<style scoped src="./post.css"></style>

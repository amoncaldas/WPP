<template>
<div>
  <box :tag="mode === 'single' ? 'main' : 'article'" :resizable="post.extra.resizable" class="post" background="white" :no-top-border="noTopBorder" v-if="post">
    <div slot="header">
      <template v-if="mode === 'single'">
        <h1 v-if="mode === 'single'">{{title}}</h1>
      </template>
      <h3 v-else-if="post.extra.no_link">{{title}}</h3>
      <template v-else>
        <a v-bind:href="buildLink(link)" :target="post.extra.target_blank ? '_blank' : '_self'" :title="title" :style="{color:$vuetify.theme.dark}" style="margin-left:0px; text-decoration:none" >
          <h3>{{title}}</h3>
        </a>
      </template>
    </div>
    <div slot="content">
      <template v-if="mode !== 'compact' && mode !== 'list'">
        <media :mode="mode" v-if="post._embedded" :media="featuredMedia" :max-height="mode === 'single'? 500 : 200"></media>
        <media :mode="mode" v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="mode === 'single'? 500 : 200"></media>
        <br>
      </template>
      <template v-if="mode === 'single'">
        <author v-if="!renderAsPage" :post="post"> </author>
        <br>
        <sharer :path="post.path" ></sharer>
        <div class="html-content" v-html="content"></div>

        <v-alert v-if="renderAsPage" :color="$vuetify.theme.dark" :value="true" icon="edit" outline type="info" >
          {{$t('post.lastUpdate') | capitalize}} <time :datetime="post.date">{{formatDateTime(post.date)}}</time>
        </v-alert>

        <div class="availability">
          <v-chip v-if="post.extra.available" color="secondary" dark :title="$t('post.available')" >{{ $t('post.available') | capitalize}}</v-chip>
          <span v-else-if="post.extra.available_at">
            <b> {{$t('post.availableAt')}} </b>
            <v-chip color="secondary" dark :title="$t('post.available_at')" >{{formatDate(post.extra.available_at)}}</v-chip>
          </span>
        </div>
        <div class="cat-and-tgs" v-if="categories.length > 0 || tags.length > 0">
          <br>
          <template v-if="categories.length > 0">
            <h4>{{$t('post.categories') | capitalize}}</h4>
            <v-btn v-for="(category) in categories" :key="category.id" round depressed :href="getTermUri(category, 'cats')" color="secondary" dark :title="category.name"  >{{category.name}}</v-btn>
          </template>
          <template v-if="tags.length > 0">
            <h4>{{$t('post.tags') | capitalize}}</h4>
            <v-btn v-for="(tag) in tags" :key="tag.id" round depressed :href="getTermUri(tag, 'p_tags')" color="secondary" dark :title="tag.name"  >{{tag.name}}</v-btn>
          </template>
        </div>
        <template v-if="hasPlaces">
          <post-map @placeClicked="placeClicked" :post="post"></post-map>
          <br>
        </template>
        <box v-if="post.extra.medias" background="white" :no-top-border="noTopBorder">
          <div slot="header">{{$t('post.gallery')}}</div>
          <div slot="content">
            <br>
            <gallery :medias="post.extra.medias" ></gallery>
          </div>
        </box>
        <br><br>
        <sharer :path="post.path" ></sharer>
        <br>
        <author mode="bio" v-if="showSingleBottomAuthor" :post="post"> </author>
      </template>
      <template v-else>
        <div v-if="post.locale !== 'neutral' && post.locale !== $store.getters.locale" class="post-locale" :title="$t('post.contentLanguage')" :style="{'border-bottom-color': $vuetify.theme.accent}"> <v-icon>language</v-icon><span> {{post.locale | uppercase}}</span></div>
        <div v-if="post.extra.sponsored" class="post-sponsored" :title="$t('post.sponsored')" :style="{'border-bottom-color': $vuetify.theme.accent}">
           <span :style="{color: $vuetify.theme.accent}"><i>{{$t('post.sponsored')}}</i></span>
          <br><br>
        </div>
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
          <div v-if="mode === 'compact' || post.extra.hide_author_bio">
            <br>
            <author v-if="!renderAsPage" :post="post"> </author>
          </div>
        </template>
        <span v-if="showType">
          <v-chip :title="$t('post.contentType')" >{{type | capitalize}}</v-chip>
        </span>
        <div style="text-align:right" v-if="!post.extra.no_link" :style="{float:showType ? 'right' : 'none'}">
          <v-btn style="margin-left:0px" :href="buildLink(link)" :target="post.extra.target_blank ? '_blank' : '_self'" :title="$t('post.readMore')" flat ><b>{{ $t('post.readMore')}}</b></v-btn>
        </div>
      </template>
    </div>
  </box>
  <template v-if="post && mode === 'single'">
    <br><br>
    <v-tabs class="post-tabs" slider-color="primary">
      <v-tab ripple v-if="!renderAsPage">
        {{ $t('post.related') }}
      </v-tab>
      <v-tab ripple>
        {{$t('post.comments')}}
      </v-tab>
      <v-tab-item v-if="!renderAsPage">
        <related :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :content-id="post.id" :max="6"> </related>
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

<template>
<div>
  <box :tag="mode === 'single' ? 'main' : 'article'" :resizable="post.extra.resizable" class="post" background="white" :no-top-border="noTopBorder" v-if="post">
    <div slot="header">
      <template v-if="mode === 'single'">
        <h1 v-html="title"></h1>
        <span v-if="$store.getters.currentSection.path !== '/'"> {{$t('post.in')}}
          <a target="_blank" @click.prevent="routeToLink($store.getters.currentSection.path)" :href="buildLink($store.getters.currentSection.path)">
            {{$store.getters.currentSection.title.rendered || $store.getters.currentSection.title}}
          </a>
        </span>
      </template>
      <h3 v-else-if="post.extra.no_link" v-html="title"></h3>
      <template v-else>
        <a @click.prevent="navigateToSingle()" :href="buildLink(link)" :target="post.extra.target_blank ? '_blank' : '_self'" :title="title" :style="{color:$vuetify.theme.dark}" style="margin-left:0px; text-decoration:none" >
          <h3>{{title}}</h3>
        </a>
      </template>
    </div>

    <div slot="content">
      <template v-if="mode !== 'compact' && mode !== 'list'">
        <div style="cursor: pointer" @click="navigateToSingle()">
          <media :mode="mode" v-if="post._embedded" :media="featuredMedia" :max-height="mode === 'single'? 500 : 200"></media>
          <media :mode="mode" v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="mode === 'single'? 500 : 200"></media>
        </div>
        <br>
      </template>
      <template v-if="mode === 'single'">
        <template v-if="!renderAsPage">
          <author-and-place :post="post"> </author-and-place>
          <br>
        </template>
        <sharer :title="title" :path="post.path" ></sharer>
        <template v-if="renderAsPage && post.extra.has_highlighted_top">
          <highlighted class="hilighted-top" position="top" :columns-per-post="6" :content-id="post.id"> </highlighted>
        </template>
        <br>
        <div class="html-prepend" v-if="prepend" v-html="prepend"></div>
        <div class="html-content" v-html="content"></div>
        <div class="html-append" v-if="append" v-html="append"></div>


        <v-alert v-if="renderAsPage && mode === 'single'" :color="$vuetify.theme.dark" :value="true" icon="edit" outline type="info" >
          {{$t('post.lastUpdate') | capitalize}} <time :datetime="post.date">{{postDate}}</time>
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
            <v-btn v-for="(category) in categories" :key="category.id" round depressed @click.prevent="routeToLink(getTermUri(category, 'cats'))" :href="getTermUri(category, 'cats')" color="secondary" dark :title="category.name"  >{{category.name}}</v-btn>
          </template>
          <template v-if="tags.length > 0">
            <h4>{{$t('post.tags') | capitalize}}</h4>
            <v-btn v-for="(tag) in tags" :key="tag.id" round depressed @click.prevent="routeToLink(getTermUri(tag, 'p_tags'))" :href="getTermUri(tag, 'p_tags')" color="secondary" dark :title="tag.name"  >{{tag.name}}</v-btn>
          </template>
        </div>
        <template v-if="renderAsPage && post.extra.has_highlighted_bottom">
          <br/>
          <highlighted position="bottom" :columns-per-post="6" :content-id="post.id"> </highlighted>
        </template>

        <template v-if="hasPlaces">
          <post-map @placeClicked="placeClicked" :post="post"></post-map>
          <br/>
        </template>
        <box v-if="post.extra.medias" background="white" :no-top-border="noTopBorder">
          <div slot="header">{{$t('post.gallery')}}</div>
          <div slot="content">
            <br>
            <gallery :medias="post.extra.medias" ></gallery>
          </div>
        </box>
        <br><br>
        <sharer :title="title" :path="post.path" ></sharer>
        <br>
        <v-btn @click.native="showReportError = true" class="report-error" > <v-icon class="notranslate" color="error">report_problem</v-icon> &nbsp;{{$t('post.reportError')}}</v-btn>
        <report-error @closed="showReportError = false" :persistent="false" v-if="showReportError"></report-error>
      </template>
      <template v-else>
        <div v-if="post.locale !== 'neutral' && post.locale !== $store.getters.locale" class="post-locale" :title="$t('post.contentLanguage')" :style="{'border-bottom-color': $vuetify.theme.accent}"> <v-icon class="notranslate">language</v-icon><span> {{post.locale | uppercase}}</span></div>
        <div v-if="post.extra.sponsored" class="post-sponsored" :title="$t('post.sponsored')" :style="{'border-bottom-color': $vuetify.theme.accent}">
          <span :style="{color: $vuetify.theme.accent}"><i>{{$t('post.sponsored')}}</i></span>
          <br><br>
        </div>
        <v-layout row wrap v-if="mode === 'list'">
          <v-flex sm3 style="padding-right:10px" @click="navigateToSingle()">
            <media :mode="mode" v-if="featuredMedia" :media="featuredMedia" :max-height="200"></media>
            <media :mode="mode" v-else-if="post.featured_media" :media-id="post.featured_media" :max-height="200"></media>
          </v-flex>
          <v-flex sm9 >
            <div v-if="excerpt && excerpt.length > 0" v-html="excerpt"></div>
          </v-flex>
        </v-layout>
        <template v-else>
          <div v-if="excerpt && excerpt.length > 0" v-html="excerpt"></div>
          <div v-if="mode === 'compact' || post.extra.hide_author_bio">
            <br>
            <author-and-place v-if="!renderAsPage" :post="post"> </author-and-place>
          </div>
        </template>
        <v-layout row wrap>
          <template>
            <v-flex v-if="showType">
              <v-chip :title="$t('post.contentType')" >{{type | capitalize}}</v-chip>
            </v-flex>
            <v-flex>
              <div style="text-align:right" v-if="!post.extra.no_link" :style="{float:showType ? 'right' : 'none'}">
                <v-btn style="margin-left:0px" @click.prevent="navigateToSingle()" :href="buildLink(link)" :target="post.extra.target_blank ? '_blank' : '_self'" :title="$t('post.readMore')" flat ><b>{{ $t('post.readMore')}}</b></v-btn>
              </div>
            </v-flex>
          </template>
        </v-layout>
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
        {{commentsTabTitle}}
      </v-tab>
      <v-tab-item v-if="!renderAsPage">
        <related :columns-per-post="$vuetify.breakpoint.mdAndUp ? 4 : 6" :content-id="post.id" :max="6"> </related>
      </v-tab-item>
      <v-tab-item>
        <comments @commentsCountUpdated="commentsCountUpdated" :open="post.comment_status === 'open'" :post-id="post.id"></comments>
      </v-tab-item>
    </v-tabs>
  </template>
</div>
</template>

<script src="./post.js"></script>
<style scoped src="./post.css"></style>

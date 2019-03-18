<template>
  <box background="white" tag="section">
    <div slot="header">
      <h2>{{boxTitle | capitalize}}</h2>
    </div>
    <div slot="content" v-if="posts">
      <template  >
        <div v-if="categories && categories.length > 0">
          <h3>{{$t('posts.categoriesFilter')}}:</h3>
          <v-chip color="secondary" :title="category" dark :key="index + '_cat'" v-for="(category, index) in categories">{{category}}</v-chip>
          <br><br>
        </div>
         <div v-if="tags && tags.length > 0">
          <h3>{{$t('posts.tagsFilter')}}:</h3>
          <v-chip color="secondary" :title="tag" dark :key="index + '_cat'" v-for="(tag, index) in tags">{{tag}}</v-chip>
          <br><br>
        </div>
        <v-container style="padding:5px" grid-list-lg fluid >
          <v-layout row wrap>
            <template v-for="(post, index) in posts">
            <v-flex v-if="index < max"  v-bind="{['sm'+columnsPerPost]: true}"  :key="post.id">
              <post :mode="mode" :key="post.id" :no-top-border="true" :post-data="post"></post>
            </v-flex>
            </template>
          </v-layout>
        </v-container>
      </template>
    </div>
    <div slot="footer">
      <div class="text-xs-left" v-if="totalPages >  1 && pagination">
        <v-layout row wrap>
          <v-flex sm6>
            <v-pagination
              v-model="currentPage"
              :length="totalPages">
            </v-pagination>
          </v-flex>
          <v-flex sm6>
            <v-btn style="float:right" :href="buildLink(archiveLink)" >{{ $t('posts.seeArchive')}}</v-btn>
          </v-flex>
          </v-layout>
        <br><br>
      </div>
      <div v-else-if="posts.length === 0 && loaded">
        <h3>{{$t('posts.noContent')}}</h3>
        <br>
      </div>
    </div>
  </box>
</template>
<script src="./posts.js"></script>

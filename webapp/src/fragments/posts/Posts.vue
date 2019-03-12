<template>
  <box background="white" tag="section">
    <div slot="header">
      <h2>{{boxTitle}}</h2>
    </div>
    <div slot="content" v-if="posts">
      <template  >
        <v-container style="padding:5px" grid-list-lg fluid >
          <v-layout row wrap>
            <template v-for="(post, index) in posts">
            <v-flex v-if="index < max"  v-bind="{['sm'+columnsPerPost]: true}"  :key="post.id">
              <post :key="post.id" :no-top-border="true" :post-data="post"></post>
            </v-flex>
            </template>
          </v-layout>
        </v-container>
      </template>
    </div>
    <div slot="footer">
      <div class="text-xs-left" v-if="totalPages >  1 && pagination">
        <v-pagination
          v-model="currentPage"
          :length="totalPages">
        </v-pagination>
        <br><br>
      </div>
    </div>
  </box>
</template>
<script src="./posts.js"></script>

<template>
  <box background="white">
    <div slot="header">
      <h3>{{boxTitle}}</h3>
    </div>
    <div slot="content" v-if="posts">
      <template  >
        <v-container style="padding:5px" grid-list-lg fluid >
          <v-layout row wrap>
            <v-flex  v-for="post in posts" v-bind="{['xs'+columnsPerPost]: true}"  :key="post.id">
              <post :key="post.id" :no-top-border="true" :post-data="post"></post>
            </v-flex>
          </v-layout>
        </v-container>
      </template>
    </div>
  </box>
</template>
<script src="./posts.js"></script>

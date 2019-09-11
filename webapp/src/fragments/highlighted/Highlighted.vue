<template>
  <box background="white" noTopBorder>
    <div slot="header">
      <h3>{{boxTitle}}</h3>
    </div>
    <div slot="content" v-if="posts">
      <v-container style="padding:5px" grid-list-lg fluid >
        <v-layout row wrap>
          <template v-for="(post) in posts">
            <v-flex v-bind="{['sm'+columns]: true}"  :key="post.id">
              <post :key="post.id" :no-top-border="true" :show-type="true" :post-data="post"></post>
            </v-flex>
          </template>
        </v-layout>
      </v-container>
    </div>
  </box>
</template>
<script src="./highlighted.js"></script>

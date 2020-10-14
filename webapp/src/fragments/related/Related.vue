<template>
  <box background="white" noTopBorder>
    <div slot="header">
      <h3>{{boxTitle}}</h3>
    </div>
    <div slot="content" v-if="posts">
      <v-container style="padding:5px" grid-list-lg fluid >
        <v-layout row wrap>
          <template v-for="(post, index) in postList">
            <v-flex v-if="index < max"  v-bind="{['xs'+columnsPerPost]: true}"  :key="post.id">
              <post :key="post.id" :no-top-border="true" :show-type="true" :post-data="post"></post>
            </v-flex>
          </template>
        </v-layout>
      </v-container>
    </div>
    <div slot="footer">
      <div class="text-xs-left" v-if="totalPages >  1 && pagination">
        <v-pagination class="notranslate"
          v-model="currentPage"
          :length="totalPages">
        </v-pagination>
        <br><br>
      </div>
    </div>
  </box>
</template>
<script src="./related.js"></script>

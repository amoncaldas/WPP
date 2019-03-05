<template>
<div>
  <box class="comment" background="white" :no-top-border="true" v-if="comments">
    <div slot="header">
      <h3>{{$t('comments.comments')}}</h3>
    </div>
    <div slot="content">
      <br>
      <template v-for="comment in comments">
        <div :key="comment.id">
          <div v-html="comment.content.rendered"></div>
        </div>
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
</div>
</template>

<script src="./comments.js"></script>
<style scoped src="./comments.css"></style>

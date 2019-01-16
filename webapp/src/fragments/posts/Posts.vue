<template>
  <box background="white">
    <div slot="header">
      <h3>{{boxTitle}}</h3>
    </div>
    <div slot="content" v-if="posts">
      <template  v-for="post in posts" >
        <post :key="post.id" :no-top-border="true" :post-data="post"></post>
        <br :key="post.id + '_'"/>
      </template>
    </div>
  </box>
</template>
<script src="./posts.js"></script>

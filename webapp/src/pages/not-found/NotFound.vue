<template>
  <v-container text-xs-center fluid-xs class="page-root not-found">
    <v-slide-y-transition mode="out-in">
      <div>
       <h2 class="big-not-found">{{$t('notFound.pageNotFound')}}</h2>
       <h3 >{{$t('notFound.tryToSearch')}}</h3>
       <search></search>
      </div>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./not-found.js"></script>

<style scoped src="./not-found.css"></style>

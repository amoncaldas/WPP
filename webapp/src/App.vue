<template>
  <v-app >
    <v-progress-linear color="neutral" class="progress-linear" :indeterminate="true" :class="{'progress-linear-disabled': !showLoading}"></v-progress-linear>
    <app-toaster></app-toaster>
    <app-header></app-header>
    <v-content class="main-container">
      <app-confirm></app-confirm>
      <app-info></app-info>
      <transition enter-active-class="animated fadeIn" leave-active-class="animated fadeOut" mode="out-in">
        <router-view v-title="title"></router-view>
      </transition>
    </v-content>
    <app-sidebar></app-sidebar>
    <app-footer></app-footer>
  </v-app>
</template>
<script src="./app.js"></script>
<style lang="scss" src="./app.scss"></style>




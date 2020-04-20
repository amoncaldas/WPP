<template>
  <v-app >
    <v-progress-linear color="neutral" class="progress-linear" :indeterminate="true" :class="{'progress-linear-disabled': !showLoading}"></v-progress-linear>
    <app-toaster></app-toaster>
    <app-header></app-header>
    <v-alert class="use-and-data-policy" v-if="showUseAndDataPolicy" :value="true" type="info" >
      <span>
        <span class='data-and-privacy-text'>{{$t('global.acceptDataAndPrivacyAndDaPolicy')}}
          <a target="_blank" class='data-and-privacy-link' v-bind:href="useAndDataPolicyUrl">{{$t('global.useAndDataPolicy')}}</a>
        </span>
      </span>
      <v-btn small @click="acceptUseAndDataPolicy()" class="use-and-data-policy-btn" >{{$t('global.ok')}}</v-btn>
    </v-alert>
    <v-content class="main-container">
      <app-confirm></app-confirm>
      <app-info></app-info>
      <transition enter-active-class="animated fadeIn" leave-active-class="animated fadeOut" mode="out-in">
        <router-view v-title="title" v-lang="lang"></router-view>
      </transition>
    </v-content>
    <app-sidebar></app-sidebar>
    <app-footer></app-footer>
  </v-app>
</template>
<script src="./app.js"></script>
<style lang="scss" src="./app.scss"></style>




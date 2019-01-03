<template>
  <v-toolbar class="ors-toolbar app-toolbar" app :clipped-left="clipped">
    <v-btn icon @click.stop="toggleSidebar" class="hidden-md-and-up">
      <v-icon>menu</v-icon>
    </v-btn>
    <a href="/">
      <img height="52.5" src="@/assets/img/logo@2x.png" :alt="$t('global.appName')" class="ml-3">
    </a>
    <v-spacer></v-spacer>
    <v-toolbar-items class="hidden-sm-and-down">
      <template v-for="(item, index) in menuItems">
        <app-h-menu :item="item" :showIcon="false" :key="index"></app-h-menu>
      </template >
    </v-toolbar-items>
  </v-toolbar>
</template>
<script src="./header.js"></script>

<style src="./header.css"></style>



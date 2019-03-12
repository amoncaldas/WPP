<template>
  <v-navigation-drawer name="sidebar" temporary disable-resize-watcher hide-overlay dark :right="right" v-model="isSideBarOpen" fixed app>
    <v-list>
      <v-list-tile @click="right = !right">
        <v-list-tile-action>
          <v-icon>compare_arrows</v-icon>
        </v-list-tile-action>
        <v-list-tile-title>{{ $t('sidebar.switchSide') }}</v-list-tile-title>
      </v-list-tile>

      <v-divider></v-divider>

      <v-list dense>
        <template v-for='(item, index) in menuItems'>
          <app-v-menu :item="item" :showIcon="true" :key="index" ></app-v-menu>
        </template>
      </v-list>
    </v-list>
  </v-navigation-drawer>
</template>
<script src="./sidebar.js"></script>

<style scoped src="./sidebar.css"></style>


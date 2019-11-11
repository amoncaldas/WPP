<template>
  <v-navigation-drawer name="sidebar" temporary disable-resize-watcher hide-overlay dark :right="right" v-model="isSideBarOpen" fixed app>
    <v-list>
      <v-list-tile class="sidebar-top" @click="right = !right">
        <v-list-tile-action>
          <v-icon class="notranslate">compare_arrows</v-icon>
        </v-list-tile-action>
        <v-list-tile-title class="sidebar-top-title-container">
          <v-layout row wrap>
            <v-flex md9 class="sidebar-top-title-text" >
              {{ $t('sidebar.switchSide') }}
            </v-flex>
            <v-spacer class="hidden-xs-and-down"></v-spacer>
            <v-flex class="sidebar-top-logo" md3>
              <a :href="buildLink('/')" class="sidebar-logo"
                :title="$store.getters.options.site_title"
                :alt="$store.getters.options.site_title"
                :style="{ backgroundImage: 'url(' + logoUrl + ')' }">
                {{$store.getters.options.site_title}}
              </a>
            </v-flex>
            <v-icon @click.stop="isSideBarOpen = false" class="notranslate right-btn-icon" :title="$t('global.close')">close</v-icon>
          </v-layout>
        </v-list-tile-title>
      </v-list-tile>

      <v-divider></v-divider>

      <v-list dense>
        <template v-for='(item, index) in menuItems'>
          <app-v-menu :item="item" :showIcon="true" :key="index" ></app-v-menu>
        </template>
      </v-list>
    </v-list>
    <!-- <v-footer class="sidebar-footer" :dark="$store.getters.isDark" color="accent" :height="40">
      <p style="margin:auto"><span>&nbsp;© {{currentYear}} <strong>{{$store.getters.options.site_title}}</strong></span></p>
    </v-footer> -->
  </v-navigation-drawer>
</template>
<script src="./sidebar.js"></script>

<style scoped src="./sidebar.css"></style>


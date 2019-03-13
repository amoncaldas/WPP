<template>
  <header>
    <v-toolbar name="main-menu" :dark="$store.getters.isDark" :color="$vuetify.theme.dark" height="65" class="wpp-toolbar app-toolbar" app :clipped-left="clipped">
      <v-btn icon @click.stop="toggleSidebar" class="hidden-md-and-up">
        <v-icon>menu</v-icon>
      </v-btn>
      <h1 class='site-title'>
			  <a :href="buildLink('/')" class="logo"
          :title="$store.getters.options.site_title"
          :alt="$store.getters.options.site_title"
          :style="{ backgroundImage: 'url(' + logoUrl + ')' }">
          {{h1}}
        </a>
			</h1>
      <v-spacer></v-spacer>
      <v-toolbar-items class="hidden-sm-and-down">
        <template v-for="(item, index) in menuItems">
          <app-h-menu :item="item" :showIcon="false" :key="index"></app-h-menu>
        </template >
      </v-toolbar-items>
      <v-btn icon :href="buildLink('/?s=')" style="padding-right:30px; padding-left:20px">
        <v-icon large>search</v-icon>
      </v-btn>
      <template v-if="$store.getters.isAuthenticated">
        <v-menu :close-on-click="true" :close-on-content-click="true" :open-on-hover="true" class="floating-menu-activator" left content-class="account-floating-menu">
          <v-btn flat slot="activator" class="floating-profile-menu-activator no-padding" :title="$t('header.myProfile')">
            <v-icon large left>personal_pin</v-icon>
          </v-btn>
            <div style="width:auto">
              <v-btn class="profile-btn no-padding" :href="buildLink('/logout')" flat :title="$t('header.logout')">
                {{$t('header.logout')}}
              </v-btn>
            </div>
            <div style="width:auto">
              <v-btn class="profile-btn no-padding" :href="buildLink('/profile')" flat :title="$t('header.myAccount')">
                {{$t('header.myAccount')}}
              </v-btn>
            </div>
        </v-menu>
      </template>
      <locale-changer></locale-changer>
    </v-toolbar>
  </header>
</template>
<script src="./header.js"></script>

<style src="./header.css"></style>



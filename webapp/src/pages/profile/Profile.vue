<template>
  <v-container fluid class="page-root profile">
    <v-slide-y-transition mode="out-in">
      <v-layout child-flex>
        <v-flex xs12>
          <box>
            <h1 slot="header">{{ $t('home.title') }}</h1>
            <div slot="content">
              <v-tabs class="tab app-tab" v-model="activeTab" slider-color="secondary">
                <v-tab ripple :key="0" >
                  {{$t('profile.profile')}}
                </v-tab>
                <v-tab-item :key="0">
                  <user-profile></user-profile>
                </v-tab-item>
              </v-tabs>
            </div>
          </box>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./profile.js"></script>

<style src="./profile.css"></style>

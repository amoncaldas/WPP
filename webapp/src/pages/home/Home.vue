<template>
  <v-container fluid class="page-root home">
    <v-slide-y-transition mode="out-in">
      <v-layout child-flex>
        <v-flex xs12>
          <box>
            <h1 slot="header">{{ $t('home.title') }}</h1>
            <div slot="content">
              <v-tabs class="tab app-tab" v-model="activeTab" slider-color="secondary">
                <v-tab ripple :key="0" >
                  {{ $t('home.tokens') }}
                </v-tab>
                <v-tab ripple :key="1" >
                  {{$t('home.profile')}}
                </v-tab>
                <v-tab-item :key="0" >
                  <app-tokens></app-tokens>
                </v-tab-item>
                <v-tab-item :key="1">
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

<script src="./home.js"></script>

<style src="./home.css"></style>

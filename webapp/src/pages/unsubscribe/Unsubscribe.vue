<template>
  <v-container text-xs-center fluid-xs class="page-root unsubscribe">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
          <box >
            <h1 slot="header" class="headline">{{ $t('unsubscribe.unsubribeFromNews') }}</h1>
            <div slot="content">
              <v-layout row wrap>
                <v-flex sm12 xs12>
                  <v-alert :value="true" outline :type="unsubscribeError === true ? 'error' : 'info'" style="color:white" >{{ info }}</v-alert>
                </v-flex>
              </v-layout>
            </div>
          </box>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./unsubscribe.js"></script>

<style scoped src="./unsubscribe.css"></style>

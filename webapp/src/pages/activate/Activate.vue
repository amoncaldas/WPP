<template>
  <v-container text-xs-center fluid-xs class="page-root activate">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
          <box >
            <h1 slot="header" class="headline">{{ $t('activate.activateYourAccount') | uppercase }}</h1>
            <div slot="content">
              <v-layout row wrap>
                <v-flex sm12 xs12>
                  <v-alert :value="true" outline :type="activationError === true ? 'error' : 'info'" style="color:white" >{{ info }}</v-alert>
                </v-flex>
              </v-layout>
            </div>
            <div slot="footer">
              <v-layout row wrap v-if="loaded">
                <v-spacer class="hidden-xs-and-down"></v-spacer>
                <v-flex xs12 sm4 :class="{'mr-2': $vuetify.breakpoint.smAndUp, 'mb-2': $vuetify.breakpoint.smAndDown}">
                  <v-btn dark block large color="secondary"  @click="resendEmail">{{ $t('activate.resendEmail') }} <v-icon class="hidden-sm-and-down" right>send</v-icon> </v-btn>
                </v-flex>
                <v-flex xs12 sm4>
                  <v-btn dark block large color="secondary"  @click="goToSignup">{{ $t('activate.signup') }} <v-icon class="hidden-sm-and-down" right>send</v-icon> </v-btn>
                </v-flex>
              </v-layout>
            </div>
          </box>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./activate.js"></script>

<style scoped src="./activate.css"></style>

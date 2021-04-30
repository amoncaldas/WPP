<template>
  <v-container text-xs-center fluid-xs class="page-root request">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
          <v-form ref="vrcForm" @keyup.native.enter="submit">
            <box >
              <h1 slot="header" class="headline">{{ $t('reset.requestPasswordReset') | uppercase }}</h1>
              <div slot="content">
                <v-text-field :label="$t('reset.usernameOrEmail')" v-model="usernameOrEmail" :rules="userNameOrEmailRules" :counter="100" required></v-text-field>
              </div>
              <div slot="footer">
                <v-layout row wrap>
                  <v-spacer class="hidden-xs-and-down"></v-spacer>
                  <v-flex xs12 sm4 :class="{'mr-2': $vuetify.breakpoint.smAndUp, 'mb-2': $vuetify.breakpoint.smAndDown}" >
                    <v-btn dark block large color="secondary" @click="goToLogin"><v-icon class="notranslate hidden-sm-and-down" left>chevron_left</v-icon>{{ $t('reset.login') }} </v-btn>
                  </v-flex>
                  <v-flex xs12 sm4>
                    <v-btn dark block large color="secondary" @click="submit">{{ $t('global.send') }} <v-icon class="notranslate hidden-sm-and-down" right>send</v-icon> </v-btn>
                  </v-flex>
                </v-layout>
              </div>
            </box>
          </v-form>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./request.js"></script>

<style scoped src="./request.css"></style>

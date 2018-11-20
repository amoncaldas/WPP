<template>
  <v-container text-xs-center fluid-xs class="page-root home">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
          <v-form ref="form" v-model="valid" @keyup.native.enter="update">
            <box >
              <h1 slot="header" class="headline">{{ $t('changePassword.resetYourPassword') | uppercase }}</h1>
              <div slot="content">
                <v-layout row wrap v-if="resetIsValid">
                  <v-flex sm12 xs12>
                      <v-text-field :label="$t('changePassword.newPassword')" v-model="resource.password1" :append-icon="passHidden ? 'visibility_off' : 'visibility'"
                      :append-icon-cb="() => (passHidden = !passHidden)" :type="passHidden ? 'password' : 'text'" counter required :rules="passwordRules"></v-text-field>
                  </v-flex>
                  <v-spacer></v-spacer>
                  <v-flex sm12 xs12>
                      <v-text-field :label="$t('changePassword.confirmNewPassword')" v-model="resource.password2" :append-icon="passHidden ? 'visibility_off' : 'visibility'"
                      :append-icon-cb="() => (passHidden = !passHidden)" :type="passHidden ? 'password' : 'text'" counter required :rules="passwordRules">
                      </v-text-field>
                  </v-flex>
                </v-layout>
                <v-layout row wrap v-else>
                  <v-flex sm12 xs12>
                    <v-alert :value="true" outline :type="loaded === true ? 'error':'info'" style="color:white" >{{ info }}</v-alert>
                  </v-flex>
                </v-layout>
              </div>
              <div slot="footer">
                <v-layout row wrap>
                  <v-spacer class="hidden-xs-and-down"></v-spacer>
                  <v-flex xs12 sm4 :class="{'mr-2': $vuetify.breakpoint.smAndUp, 'mb-2': $vuetify.breakpoint.smAndDown}" >
                    <v-btn dark block large color="secondary" @click="goToLogin"><v-icon class="hidden-sm-and-down" left>chevron_left</v-icon>{{ $t('changePassword.login') }} </v-btn>
                  </v-flex>
                  <v-flex xs12 sm4>
                    <v-btn dark block large color="secondary" v-if="resetIsValid" @click="update">{{ $t('changePassword.changePassword') }} <v-icon class="hidden-sm-and-down" right>send</v-icon> </v-btn>
                    <v-btn dark block large color="secondary" v-else  @click="goToRequest">{{ $t('changePassword.restartReset') }} <v-icon class="hidden-sm-and-down" right>send</v-icon> </v-btn>
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

<script src="./reset.js"></script>

<style scoped src="./reset.css"></style>

<template>
  <div>
    <v-form ref="form" @keyup.native.enter="submit">
      <box :no-top-border="!topBorder">
        <h1 slot="header" class="headline">{{ $t('authentication.signIn') | uppercase }}</h1>
        <div slot="content">
          <a class="social-gh" v-if="$store.getters.options.signup_with_github" @click="socialAuthentication('github')">
            <div></div>
            <span class="social-gh-title">{{$t('authentication.signupWithGithub')}}</span>
          </a>

          <div class="social-or" v-if="$store.getters.options.signup_with_github">
            <div class="social-orText">or</div>
            <div class="social-orStroke"></div>
          </div>

          <p>{{ $t('authentication.loginSubtitle') }}</p>

          <v-text-field :label="$t('authentication.username')" v-model="username" :rules="userNameRules" :counter="20" required></v-text-field>
          <v-text-field
            :label="$t('authentication.password')"
            v-model="password"
            required
            :append-icon="hidePass ? 'visibility_off' : 'visibility'"
            :append-icon-cb="() => (hidePass = !hidePass)"
            :type="hidePass ? 'password' : 'text'"
            counter
            :rules="passwordRules"
          ></v-text-field>
        </div>
        <div slot="footer">
          <v-layout row wrap>
            <v-spacer class="hidden-xs-and-down"></v-spacer>
            <v-flex xs12 sm4 :class="{'mr-2': $vuetify.breakpoint.smAndUp, 'mb-2': $vuetify.breakpoint.smAndDown}" >
              <v-btn dark block large color="secondary" @click="goToReset">{{ $t('authentication.resetPass') }} </v-btn>
            </v-flex>
            <v-flex xs12 sm4>
              <v-btn dark block large color="secondary" @click="submit">{{ $t('authentication.login') }} <v-icon right>send</v-icon> </v-btn>
            </v-flex>
          </v-layout>
        </div>
      </box>
    </v-form>
  </div>
</template>

<script src="./authentication.js"></script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style src="./authentication.css" scoped></style>


<template>
  <v-container text-xs-center fluid-xs class="page-root sigup">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
            <br><br><br>
            <box>
              <h1 slot="header" class="headline">{{ $t('signup.register') | uppercase }}</h1>
              <div slot="content">
                <a class="social-gh" v-if="$store.getters.options.signup_with_github" @click="socialRegistration('github')">
                  <div></div>
                  <span class="social-gh-title">{{$t('signup.signupWithGithub')}}</span>
                </a>

                <div class="social-or" v-if="$store.getters.options.signup_with_github">
                  <div class="social-orText">or</div>
                  <div class="social-orStroke"></div>
                </div>

                <user-form :submitFn="submit"></user-form>
                <vue-recaptcha v-if="$store.getters.options.recaptcha_site_key"
                  :sitekey="$store.getters.options.recaptcha_site_key"
                  ref="recaptcha"
                  size="invisible"
                  @verify="onCaptchaVerified"
                  @expired="onCaptchaExpired">
                </vue-recaptcha>
              </div>
            </box>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./signup.js"></script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style src="./signup.css" scoped></style>

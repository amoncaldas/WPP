<template>
  <v-container text-xs-center fluid-xs class="page-root home">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
          <br><br><br><br>
          <v-form ref="form" @keyup.native.enter="submit">
            <box>
              <h1 slot="header" class="headline">{{ $t('login.signIn') | uppercase }}</h1>
              <div slot="content">
                <a class="social-gh" v-if="$store.getters.options.signup_with_github" @click="socialAuthentication('github')">
                  <div></div>
                  <span class="social-gh-title">{{$t('login.signupWithGithub')}}</span>
                </a>

                <div class="social-or" v-if="$store.getters.options.signup_with_github">
                  <div class="social-orText">or</div>
                  <div class="social-orStroke"></div>
                </div>

                <p>{{ $t('login.loginSubtitle') }}</p>

                <v-text-field :label="$t('login.username')" v-model="username" :rules="userNameRules" :counter="20" required></v-text-field>
                <v-text-field
                  :label="$t('login.password')"
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
                    <v-btn dark block large color="secondary" @click="goToReset">{{ $t('login.resetPass') }} </v-btn>
                  </v-flex>
                  <v-flex xs12 sm4>
                    <v-btn dark block large color="secondary" @click="submit">{{ $t('login.login') }} <v-icon right>send</v-icon> </v-btn>
                  </v-flex>
                </v-layout>
              </div>
            </box>
          </v-form>
          <blockquote class="quote">
              &#8220;{{ $t('login.quote') }}&#8221;
              <footer>
                <small>
                  <em>&mdash;{{ $t('login.quoteAuthor') }}</em>
                </small>
              </footer>
            </blockquote>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./auth.js"></script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style src="./auth.css" scoped></style>


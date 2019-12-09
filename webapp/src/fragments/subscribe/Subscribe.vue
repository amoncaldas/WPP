<template>
  <box tag="section" :no-top-border="!topBorder">
    <h1 slot="header" class="headline">{{ $t('subscribe.pageTitle') | uppercase }}</h1>
    <v-form slot="content" ref="form" @keyup.native.enter="submit">
      <div>
        <v-text-field :label="$t('subscribe.email')" v-model="resource.email" :rules="emailRules" :counter="30" required></v-text-field>
        <v-text-field
          :label="$t('subscribe.name')"
          v-model="resource.name"
          required
          type="text"
          :counter="100"
          :rules="nameRules">
        </v-text-field>
      </div>
      <vue-recaptcha v-if="$store.getters.options.recaptcha_site_key" :sitekey="$store.getters.options.recaptcha_site_key"
        ref="recaptcha"
        size="invisible"
        @verify="onCaptchaVerified"
        @expired="onCaptchaExpired">
      </vue-recaptcha>
    </v-form>
    <div slot="footer">
      <v-layout row wrap>
        <v-flex xs12 sm8 :class="{'mb-2': $vuetify.breakpoint.smAndDown}" >
          <div v-if="locales.length > 1">
            <v-select class="notranslate"
              required
              v-model="locale"
              item-text="title"
              item-value="value"
              :items="locales"
              :label="$t('subscribe.locale')">
            </v-select>
          </div>
        </v-flex>
        <v-spacer class="hidden-xs-and-down"></v-spacer>
        <v-flex xs12 sm3>
          <v-btn v-if="ready" dark block large color="secondary" @click="submit">{{ $t('global.send') }} <v-icon class="notranslate" right>send</v-icon> </v-btn>
        </v-flex>
      </v-layout>
    </div>
  </box>
</template>

<script src="./subscribe.js"></script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style src="./subscribe.css" scoped></style>


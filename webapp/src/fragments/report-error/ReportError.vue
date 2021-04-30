<template>
  <v-dialog v-model="visible" max-width="600" class="auth-modal" :persistent="true">
    <box tag="main" class="report-error" @closed="close" background="white" :closable="true">
      <div slot="header">
        <h2>{{$t('reportError.title')}}</h2>
      </div>
      <div slot="content">
        <v-form ref="vrcForm">
          <v-text-field :label="$t('reportError.url')" v-model="resource.url" disabled autofocus required></v-text-field>
          <v-textarea auto-grow class="report-error-message" :height="$vuetify.breakpoint.mdAndUp ? 200 : 100" type="text"
            hide-details counter :label="$t('reportError.yourMsg')" box :title="$t('reportError.yourMsg')"
            v-model="resource.message" :required="true">
          </v-textarea>
          <div class="report-error-btn-container" v-if="ready">
            <v-btn color="secondary" left @click.native="submit">{{$t('global.send')}}</v-btn>
          </div>
          <vue-recaptcha :sitekey="$store.getters.options.recaptcha_site_key" ref="recaptcha" size="invisible"
            @verify="onCaptchaVerified" @expired="onCaptchaExpired">
          </vue-recaptcha>
        </v-form>
      </div>
    </box>
  </v-dialog>
</template>

<script src="./report-error.js"></script>
<style scoped src="./report-error.css"></style>

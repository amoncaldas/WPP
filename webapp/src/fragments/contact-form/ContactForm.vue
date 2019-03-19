<template>
  <div>
    <box tag="main" class="contact-form" background="white">
      <div slot="header">
        <h2>{{$t('contactForm.title')}}</h2>
      </div>
      <div slot="content">
        <v-form ref="form">
          <v-text-field :label="$t('contactForm.subject')" v-model="resource.subject" autofocus required></v-text-field>
          <v-textarea auto-grow class="contact-message" :height="$vuetify.breakpoint.mdAndUp ? 300 : 150" type="text" hide-details counter
            :label="$t('contactForm.yourMsg')" box
            :title="$t('contactForm.yourMsg')"
            v-model="resource.content"
            :required="true">
          </v-textarea>
          <div class="contact-btn-container" v-if="ready">
            <v-btn color="secondary" left @click.native="submit">{{$t('global.send')}}</v-btn>
          </div>
          <vue-recaptcha :sitekey="$store.getters.options.recaptcha_site_key"
            ref="recaptcha"
            size="invisible"
            @verify="onCaptchaVerified"
            @expired="onCaptchaExpired">
          </vue-recaptcha>
        </v-form>
      </div>
    </box>
  </div>
</template>

<script src="./contact-form.js"></script>
<style scoped src="./contact-form.css"></style>

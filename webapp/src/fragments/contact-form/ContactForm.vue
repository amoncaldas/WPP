<template>
  <div>
    <box tag="main" class="contact-form" background="white">
      <div slot="header">
        <h2>{{$t('contactForm.title')}}</h2>
      </div>
      <div slot="content">
        <vrc-form 
          ref="vrcForm"
          :vm="_self"
          :sendTitle="$t('global.send')" 
          :httpOptions="httpOptions"
          
          :crud-options="vrcOptions"
          mode="create"
          :content-name="$t('contactForm.yourMsg')"
          endpoint="wpp/v1/message/send">
          <div slot="default" v-if="crudReady">
            <v-text-field :label="$t('contactForm.subject')" 
              v-model="resource.subject" 
              autofocus 
              required>
            </v-text-field>
            <v-text-field
              :label="$t('contactForm.name')"
              ref="name"
              class="name"
              v-model="resource.name"
              required
              :counter="20">
            </v-text-field>
            <v-text-field
              ref="email"
              :label="$t('contactForm.email')"
              class="user-email"
              v-model="resource.email"
              :rules="emailRules"
              required>
            </v-text-field>          
            <v-textarea auto-grow class="contact-message" :height="$vuetify.breakpoint.mdAndUp ? 300 : 150" type="text" hide-details counter
              :label="$t('contactForm.yourMsg')" box
              :title="$t('contactForm.yourMsg')"
              v-model="resource.message"
              :required="true">
            </v-textarea>
          </div>
        </vrc-form>
      </div>
    </box>
  </div>
</template>

<script src="./contact-form.js"></script>
<style scoped src="./contact-form.css"></style>

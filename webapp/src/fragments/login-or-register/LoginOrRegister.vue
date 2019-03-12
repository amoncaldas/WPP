<template>
  <v-dialog v-model="visible" max-width="600" class="auth-modal" :persistent="true">
      <box v-model="active" :resizable="true" :closable="!persistent" @closed="close" background="white">
        <div slot="header">
          <h3>{{ $t('loginOrRegister.authentication') }}</h3>
        </div>
        <div slot="content">
          <v-tabs class="auth-tabs" v-model="activeTab" slider-color="primary">
            <v-tab ripple>
              {{ $t('loginOrRegister.login') }}
            </v-tab>
            <v-tab ripple >
              {{$t('loginOrRegister.registration')}}
            </v-tab>
            <v-tab-item>
              <authentication :top-border="false" :on-authenticate="afterLogin"></authentication>
            </v-tab-item>
            <v-tab-item>
              <signup :on-signup="afterSignup"></signup>
            </v-tab-item>
          </v-tabs>
        </div>
        <div slot="footer" class="text-right">
        </div>
      </box>
    </v-dialog>
</template>

<script src="./login-or-register.js"></script>
<style scoped src="./login-or-register.css"></style>

<template>
  <v-form ref="form" v-if="crudReady" @keyup.native.enter="submit">
    <v-text-field
      :label="$t('user.username')"
      ref="username"
      class="username"
      :class="validatableInputClass(usernameValid)"
      v-model="resource.username"
      :rules="userNameRules"
      @keyup="userNameChanged"
      :counter="20"
      :readonly="mode === 'edit'"
      :append-icon="validatableInputStateIcon(usernameValid)">
    </v-text-field>
    <v-text-field
      ref="userEmail"
      :label="$t('user.email')"
      class="user-email"
      :class="validatableInputClass(emailValid)"
      :append-icon="validatableInputStateIcon(emailValid)"
      @keyup="emailChanged"
      :readonly="mode === 'edit' && resource.email === resource.username"
      v-model="resource.email"
      :rules="emailRules"
      required>
      </v-text-field>
    <v-layout row wrap>
      <v-flex xs6>
        <v-text-field :label="$t('user.firstName')" v-model="resource.first_name" required></v-text-field>
      </v-flex>
      <v-flex xs6>
        <v-text-field :label="$t('user.lastName')" v-model="resource.last_name" required></v-text-field>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12>
        <v-text-field :label="$t('user.website')" v-model="resource.website" :rules="websiteRules"></v-text-field>
      </v-flex>
    </v-layout>

    <br>
    <h4 class="text-xs-left">{{changePasswordLabel}}</h4>
    <v-layout row wrap>
      <v-flex sm5 xs12>
          <v-text-field :label="$t('user.newPassword')" v-model="resource.password" :append-icon="passVisibility ? 'visibility_off' : 'visibility'"
          @click:append="passVisibility = !passVisibility" :type="passVisibility ? 'password' : 'text'" counter :required="mode === 'create'"></v-text-field>
      </v-flex>
      <v-spacer></v-spacer>
      <v-flex sm5 xs12>
          <v-text-field :label="$t('user.confirmNewPassword')" v-model="resource.confirmPassword" :append-icon="passVisibility ? 'visibility_off' : 'visibility'"
          @click:append="passVisibility = !passVisibility" :type="passVisibility ? 'password' : 'text'" counter :required="mode === 'create'"
          :rules="passwordRules"></v-text-field>
      </v-flex>
    </v-layout>

    <v-layout row wrap>
      <v-flex sm5 xs12>
        <v-switch class="receive-news"
          :label="$t('user.receiveNews')" @change="newsChanged" v-model="receiveNews"
        ></v-switch>
      </v-flex>
      <v-flex sm5 xs12>
        <v-switch class="notranslate" v-if="hasUseAndDataPolicyPage" required v-model="dataAndPrivacyPolicyAccepted"    >
          <template slot='label'>
            <span style="display:inline-block">{{$t('user.IAccept')}}
              <a target="_blank" class='data-and-privacy-link' v-bind:href="useAndDataPolicyUrl">{{$t('global.useAndDataPolicy')}}</a>
            </span>
          </template>
        </v-switch>
      </v-flex>
    </v-layout>

    <v-layout row wrap>
      <v-spacer></v-spacer>
      <v-btn dark large color="secondary" v-if="crudReady" @click="submit">{{ $t('global.send') }}
        <v-icon class="notranslate" right>send</v-icon>
      </v-btn>
    </v-layout>
  </v-form>
</template>

<script src="./user.js"></script>
<style lang="scss" src="./user.scss"></style>

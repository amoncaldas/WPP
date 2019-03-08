<template>
  <v-container text-xs-center fluid-xs class="page-root sigup">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
          <signup :on-signup="afterSignup"></signup>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./register.js"></script>

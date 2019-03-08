<template>
  <v-container text-xs-center fluid-xs class="page-root authentication">
    <v-slide-y-transition mode="out-in">
      <v-layout row wrap>
        <v-flex xs12 xl6 sm12 md8 lg8 md10 offset-md2>
          <br><br><br><br>
          <authentication :on-authenticate="redirectToProfile"></authentication>
        </v-flex>
      </v-layout>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./login.js"></script>


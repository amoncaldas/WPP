<template>
  <v-container text-xs-center fluid-xs class="page-root sigup">
    <v-slide-y-transition mode="out-in">
      <signup :on-signup="afterSignup"></signup>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./register.js"></script>

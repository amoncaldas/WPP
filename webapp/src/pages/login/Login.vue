<template>
  <v-container text-xs-center fluid-xs class="page-root authentication">
    <v-slide-y-transition mode="out-in">
     <Authentication :on-authenticate="redirectToProfile"></Authentication>
    </v-slide-y-transition>
  </v-container>
</template>

<script src="./login.js"></script>


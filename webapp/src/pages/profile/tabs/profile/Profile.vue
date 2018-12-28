<template>
  <v-card>
    <v-card-text>
      <user-form :userId="$store.getters.user.id"></user-form>
    </v-card-text>
  </v-card>
</template>
<script src="./profile.js"></script>

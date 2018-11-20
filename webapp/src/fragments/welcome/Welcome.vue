<template>
  <div v-if="user.displayName" class="welcome">
    <p >Hi {{user.displayName}}</p>
  </div>
</template>
<script src="./welcome.js"></script>
<style scoped src="./welcome.css"></style>


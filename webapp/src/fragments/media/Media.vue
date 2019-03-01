<template>
  <div v-if="url" class="image-container" :class="{'list-mode': isListMode}">
    <img :src="url" :alt="title" :title="title" class="image">
  </div>
</template>
<script src="./media.js"></script>
<style scoped src="./media.css"></style>

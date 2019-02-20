<template>
  <v-img
    :max-width="maxWidth"
    position="top center"
    :max-height="maxHeight"
    :contains="contains" v-if="url" :src="url"
    lazy-src="/static/img/image-1024x800.jpg"
    style="max-width:100%"
    :alt="title">
  </v-img>
</template>
<script src="./media.js"></script>

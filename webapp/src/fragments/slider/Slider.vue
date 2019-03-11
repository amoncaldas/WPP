<template>
  <vue-flux class="slider"
   :options="fluxOptions"
   :captions="fluxCaptions"
   :images="fluxImages"
   :transitions="fluxTransitions"
   ref="slider">
    <flux-caption slot="caption"></flux-caption>
    <flux-controls slot="controls"></flux-controls>
    <flux-index ref="fluxIndex" slot="index"></flux-index>
    <flux-pagination slot="pagination"></flux-pagination>
  </vue-flux>
</template>
<script src="./slider.js"></script>
<style scoped src="./slider.css"></style>


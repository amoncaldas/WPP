<template>
  <div class="sharer">
    <a class="item"  target="_blank" :href="'https://www.facebook.com/sharer/sharer.php?u=' + link" :title="$t('sharer.shareViaFacebook')">
     <img class="sharer-img" src="@/assets/img/logo-facebook.svg" :alt="$t('sharer.shareViaFacebook')">
    </a>
    <a class="item" target="_blank" :href="'https://twitter.com/share?url=' + link" :title="$t('sharer.shareViaTwitter')">
      <img class="sharer-img" src="@/assets/img/logo-twitter.svg" :alt="$t('sharer.shareViaFacebook')">
    </a>
    <a class="item" target="_blank" :href="'http://www.pinterest.com/pin/create/button/?url=' + link" :title="$t('sharer.shareViaPinterest')">
      <img class="sharer-img" src="@/assets/img/logo-pinterest.svg" :alt="$t('sharer.shareViaPinterest')">
    </a>
    <a class="item" target="_blank" :href="'https://wa.me/whatsappphonenumber/?text=' + link" data-action="share/whatsapp/share" :title="$t('sharer.shareViaWhatsapp')" >
      <img class="sharer-img" src="@/assets/img/logo-whatsapp.svg" :alt="$t('sharer.shareViaWhatsapp')">
    </a>
  </div>
</template>

<script src="./sharer.js"></script>

<style src="./sharer.css"></style>

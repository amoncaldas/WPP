<template>
  <transition enter-active-class="animated fadeIn" leave-active-class="animated fadeOut" mode="out-in">
    <wrapper-tag :style="{backgroundColor: backgroundColor}" :tag="tag" v-top-border:color="topBorderColor" class="box" :id="guid" :class="classes" v-if="!closed">
      <div class="box-header" v-if="hasHeaderSlot"  v-bg:palette="headerBackground">
        <v-icon color="primary" slot="title" @click="close()" v-if="closable" class="notranslate right-btn-icon" :title="$t('global.close')">close</v-icon>
        <v-icon color="dark" slot="title" @click="resize(false)" v-if="resizable && maximized" class="notranslate right-btn-icon" :title="$t('box.minimize')">aspect_ratio</v-icon>
        <v-icon color="dark" slot="title" @click="resize(true)" v-if="resizable && !maximized" class="notranslate right-btn-icon" :title="$t('box.maximize')">aspect_ratio</v-icon>
        <slot name="header"></slot>
      </div>
      <div class="box-content" :class="{maximized: maximized}">
        <slot v-if="hasContentSlot" name="content"></slot>
        <slot v-if="hasDefaultSlot"></slot>
      </div>
      <div class="box-footer" v-if="hasFooterSlot">
        <slot name="footer"></slot>
      </div>
    </wrapper-tag>
  </transition>
</template>

<script src="./box.js"></script>
<style scoped src="./box.css" scoped></style>


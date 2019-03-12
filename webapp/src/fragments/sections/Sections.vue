<template>
  <box background="white" tag="section">
    <div slot="header">
      <h3>{{boxTitle}}</h3>
    </div>
    <div slot="content" v-if="sections">
      <template  >
        <v-layout row wrap>
          <v-flex  v-for="section in sections" v-bind="{['sm'+columnsPerSection]: true}"  :key="section.id">
            <post :key="section.id" :no-top-border="true" :post-data="section"></post>
            <br :key="section.id + '_'"/>
          </v-flex>
        </v-layout>
      </template>
    </div>
  </box>
</template>
<script src="./sections.js"></script>
<style scoped src="./sections.css"></style>

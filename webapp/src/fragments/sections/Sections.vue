<template>
  <box background="white" tag="section">
    <div slot="header">
     <h2>{{boxTitle | capitalize}}</h2>
    </div>
    <div slot="content" v-if="sections.length > 0">
      <template  >
        <v-layout row wrap>
          <v-flex  v-for="section in sections" v-bind="{['sm'+columnsPerSection]: true}"  :key="section.id">
            <post :key="section.id" :no-top-border="true" :post-data="section"></post>
            <br :key="section.id + '_'"/>
          </v-flex>
        </v-layout>
      </template>
    </div>
    <div v-else-if="loaded">
      <h3>{{$t('sections.noContent')}}</h3>
    </div>
  </box>
</template>
<script src="./sections.js"></script>
<style scoped src="./sections.css"></style>

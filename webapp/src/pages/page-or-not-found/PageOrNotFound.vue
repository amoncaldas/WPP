<template>
<v-container grid-list-lg fluid class="page-root page" >
  <v-layout row wrap>
    <v-flex v-bind="{[ hasSidebar? 'md9' : 'md12']: true }" >
      <not-found-component v-if="notFound"></not-found-component>
      <post v-else-if="loaded" :is-page="true" :post-data="post" mode="single"></post>
    </v-flex>
    <v-flex md3 v-if="hasSidebar" >
      <template v-if="showNewsletterForm">
        <subscribe small :topBorder="true"></subscribe>
        <br><br>
      </template>
      <sections></sections>
    </v-flex>
  </v-layout>
</v-container>
</template>
<script src="./page-or-not-found.js"></script>

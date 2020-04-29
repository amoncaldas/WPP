<template>
<v-container grid-list-lg fluid class="page-root single" >
  <v-layout row wrap>
    <v-flex v-bind="{[ hasSidebar? 'md9' : 'md12']: true }" >
      <not-found-component v-if="notFound"></not-found-component>
      <post v-else-if="loaded" :post-data="post" mode="single"></post>
    </v-flex>
    <v-flex md3 v-if="hasSidebar">
      <template v-if="showNewsletterForm">
        <subscribe small :topBorder="true"></subscribe>
        <br><br>
      </template>
      <template v-for="postType in sidebarPostTypes">
        <template v-if="postType.endpoint === 'sections'">
          <sections :key="postType.endpoint" :max="maxInSidebar"></sections>
          <br :key="postType.endpoint + 'br'">
        </template>
        <template v-else>
          <posts :parent-id="parentSectionId" :max="maxInSidebar" :exclude="[post.id]" :columns-per-post="12" :key="postType.endpoint" :endpoint="postType.endpoint" :title="postType.title"></posts>
        </template>
      </template>
    </v-flex>
  </v-layout>
</v-container>
</template>
<script src="./single.js"></script>

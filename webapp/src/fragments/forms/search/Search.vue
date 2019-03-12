<template>
  <div>
    <v-form ref="form" @keyup.native.enter="submit">
      <v-text-field
        :label="$t('searchComponent.placeholder')"
        ref="searchInput"
        class="search-box"
        v-model="term"
        @keyup="search"
        append-icon="search">
      </v-text-field>
    </v-form>
    <post v-for="post in results" mode="list" :key="post.id" :no-top-border="true" :post-data="post"></post>

    <div class="text-xs-left" v-if="totalPages >  1 && pagination">
        <v-pagination
          v-model="currentPage"
          :length="totalPages">
        </v-pagination>
        <br><br>
      </div>
  </div> 
</template>

<script src="./search.js"></script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style src="./search.css" scoped></style>

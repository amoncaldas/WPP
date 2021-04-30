<template>
  <div>
    <v-form ref="vrcForm" @submit.prevent>
       <v-layout row wrap>
        <v-flex xs12 v-bind="{['sm'+ searchInputColumns]: true}" >
            <v-text-field height="60" box 
              :style="{paddingRight: $vuetify.breakpoint.smAndDown? '0px': '5px'}"
              :label="placeHolder"
              ref="searchInput"
              class="search-box"
              v-model="term"
              @keyup="runSearchWithEnter"
              hide-details>
              <template slot="append">
                <v-icon class="notranslate" :title="$t('searchComponent.search')" @click="doSearch()">search</v-icon>
                <v-icon class="notranslate" :title="$t('searchComponent.clear')" v-if="term" @click="term = ''">clear</v-icon>
              </template>
            </v-text-field>
        </v-flex>
        <v-flex xs12 sm4 v-if="searchInputColumns === 8">
          <v-select height="60" clearable box class="notranslate"
            v-model="section"
            @change="search"
            item-text="title.rendered"
            item-value="id"
            hide-details
            :items="searchableSections"
            :label="$t('searchComponent.section')">
          </v-select>
        </v-flex>
      </v-layout>
      <br>
      <template v-if="searched">
        <h2 class="results-title" v-if="results.length > 0">{{resultsTitle}}</h2>
        <h2 class="results-title" v-else>{{$t('searchComponent.noResult')}}</h2>
        <br>
      </template>
    </v-form>
    <template v-if="searched">
      <post v-for="post in results" mode="list" class="search-result" :show-type="true" :key="post.id" :no-top-border="true" :post-data="post"></post>
    </template>
    <br>
    <div class="text-xs-left" v-if="totalPages > 1">
        <v-pagination class="notranslate"
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

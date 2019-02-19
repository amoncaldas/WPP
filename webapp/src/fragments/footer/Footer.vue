<template>
  <v-footer dark :height="height" :class="{'small': $vuetify.breakpoint.smAndDown}">
    <v-layout row wrap>
      <v-flex xs12>
        <template  v-for="(item, index) in menuItems">
          <app-h-menu :item="item" :showIcon="false" :key="index"></app-h-menu>
        </template >
      </v-flex>
       <v-flex xs12>
          <p>
            <span>&nbsp;© {{currentYear}} <strong>{{footerMainSiteName}}</strong></span>
            <span class="hidden-sm-and-down"> | {{ $t('global.developedByIntro') }}
              <strong>
                <a class="hidden-sm-and-down" target="_blank" :href="developedByLink">{{ $t('global.developedBy') }}</a>
              </strong>
            </span>
          </p>
      </v-flex>
    </v-layout>
  </v-footer>
</template>

<script src="./footer.js">

</script>

<style src="./footer.css" scoped></style>



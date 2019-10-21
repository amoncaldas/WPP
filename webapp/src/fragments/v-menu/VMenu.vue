<template>
  <v-expansion-panel v-if='item.items && showMenuItem(item)' class="no-shadow" :value="item.href === $store.getters.currentSection.path ? 0 : null">
    <v-expansion-panel-content class="v-menu-expansion-panel">
      <div @click.stop="nav(item)" slot="header"><h4 >{{ item.title }}</h4></div>
      <template v-if="!item.items">
        <v-list-tile @click.stop="nav(item)" :href="item.href" class="v-menu-item" :class="itemClass" :title="item.title">
          <v-list-tile-action>
            <v-icon class="notranslate"> {{ item.icon }}</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title> {{ item.title }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </template>
      <template v-else-if="showMenuItem(item)">
        <template v-for="itemLevel2 in item.items">
          <v-expansion-panel v-if="itemLevel2.items && showMenuItem(itemLevel2)" :key="itemLevel2.href" class="no-shadow" :value="null">
            <v-expansion-panel-content class="v-menu-expansion-panel level-2">
                <div @click.stop="nav(item)" slot="header"><h4 >{{ itemLevel2.title }}</h4></div>
                <v-list-tile @click.stop="nav(itemLevel3)" v-for='itemLevel3 in itemLevel2.items' :key="itemLevel3.href" :href="itemLevel3.href" class="v-menu-item" :class="itemClass" :title="itemLevel2.title">
                  <v-list-tile-action>
                    <v-icon class="notranslate"> {{ itemLevel3.icon }}</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <v-list-tile-title> {{ itemLevel3.title }}</v-list-tile-title>
                  </v-list-tile-content>
                </v-list-tile>
            </v-expansion-panel-content>
          </v-expansion-panel>
          <v-list-tile v-else-if="itemLevel2" :key="itemLevel2.href" @click.stop="nav(itemLevel2)" :href="itemLevel2.href" class="v-menu-item" :class="itemClass" :title="itemLevel2.title">
            <v-list-tile-action>
              <v-icon class="notranslate"> {{ itemLevel2.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title> {{ itemLevel2.title }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </template>
      </template>
    </v-expansion-panel-content>
  </v-expansion-panel>

  <v-subheader :key="item.href" v-else-if="item.header && showMenuItem(item)"> {{ item.header }}</v-subheader>
  <v-divider :key="item.href" v-else-if="item.divider && showMenuItem(item)"></v-divider>

  <v-list-tile :key="item.href" v-else-if="showMenuItem(item)" class="v-menu-item" :class="itemClass" :href="item.href" @click.stop="nav(item)" ripple v-bind:disabled="item.disabled"
    :title="item.title">
    <v-list-tile-action>
      <v-icon class="notranslate"> {{ item.icon }} </v-icon>
    </v-list-tile-action>
    <v-list-tile-content>
      <v-list-tile-title> {{ item.title}} </v-list-tile-title>
    </v-list-tile-content>
    <v-list-tile-action v-if='item.subAction'>
      <v-icon class="notranslate success--text"> {{ item.subAction }}</v-icon>
    </v-list-tile-action>
  </v-list-tile>
</template>


<script src="./v-menu.js"></script>
<style src="./v-menu.css"></style>


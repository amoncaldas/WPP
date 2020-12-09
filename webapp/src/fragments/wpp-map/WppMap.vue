<template>
  <box tag="section" resizable background="white" @boxCreated="boxCreated" @resized="adjustMap" v-if="loaded">
    <div slot="header">
      <h2 v-html="title"></h2>
    </div>
    <l-map ref="map" :max-zoom="maxZoom" :center="mapCenter" style="z-index:3" :zoom="zoom" class="post-map" :style="{height: mapHeight}">
      <l-marker v-for="(marker, index) in markers" :lat-lng="marker.position" :key="index+'-marker'" :icon="marker.icon">
        <l-popup v-if="marker.label">
          <div >
            <span v-html="marker.label"></span>
            <v-icon v-if="marker.data" @click="markerInfoClick(marker)" color="info" class="notranslate right-btn-icon pointer">launch</v-icon>
          </div>
        </l-popup>
      </l-marker>
      <template v-for="(route, index) in routes">
        <l-polyline :key="index"  
        :lat-lngs="route.polyline" 
        :weight="10" color="white"
        :dash-array="polylineDashedArray(route)"
        :dash-offset="polylineDashedOffset(route)"
        >
        </l-polyline>
      </template>
      <template v-for="(route) in routes">
        <l-polyline :key="route.id"  :lat-lngs="route.polyline" :weight="7" 
          :color="getColorByTransportation(route.means_of_transportation)"
          :dash-array="polylineDashedArray(route)"
          :dash-offset="polylineDashedOffset(route)"          >
          <l-tooltip v-html="route.title"></l-tooltip>
        </l-polyline>
      </template>
       <l-control-layers
        :position="layersPosition"
        :collapsed="true" />
       <l-tile-layer
        v-for="tileProvider in tileProviders"
        :key="tileProvider.name"
        :name="tileProvider.name"
        :visible="tileProvider.visible"
        :url="tileProvider.url"
        :attribution="tileProvider.attribution"
        :token="tileProvider.token"
        layer-type="base"/>
    </l-map>
  </box>
  <box v-else tag="div" background="white">
    <div slot="header">
      <h2 v-html="$t('wppMap.aMapWillBeLoadedHere')"></h2>
    </div>
    <div class="map-place-holder" :style="{height: mapHeight}">
      <v-layout fill-height align-center justify-center ma-0 >
        <v-progress-circular indeterminate color="black"></v-progress-circular>
      </v-layout>
    </div>
  </box>
</template>

<script src="./wpp-map.js"></script>
<style scoped src="./wpp-map.css"></style>

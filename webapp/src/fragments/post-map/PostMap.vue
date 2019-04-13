<template>
  <box tag="section" resizable background="white" @boxCreated="boxCreated" @resized="adjustMap" v-if="loaded">
    <div slot="header">
          <h2>{{title}}</h2>
        </div>
    <v-alert :value="info" outline type="info" style="color:white" >{{ info }}</v-alert>
    <l-map ref="map" :max-zoom="maxZoom" style="z-index:3" :zoom="zoom" class="section-map" :style="{height: mapHeight + 'px'}">
      <l-marker v-for="(marker, index) in markers" :lat-lng="marker.position" :key="index+'-marker'" :icon="marker.icon">
        <l-popup v-if="marker.label">
            <div >
              {{marker.label}}
              <v-icon v-if="marker.json" @click="markerInfoClick(marker)" color="info" class="right-btn-icon pointer">launch</v-icon>
            </div>
          </l-popup>
      </l-marker>
      <l-polyline v-if="polyline" :lat-lngs="polyline" :weight="7" :color="routeColor">
        <!-- <l-tooltip v-html="routeToolTip"></l-tooltip> -->
      </l-polyline>
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
</template>

<script src="./post-map.js"></script>
<style scoped src="./post-map.css"></style>

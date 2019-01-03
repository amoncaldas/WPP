<template>
  <box resizable background="white" @boxCreated="boxCreated" @resized="adjustMap">
    <div slot="header">
          <h3>{{$t('sectionsMap.title')}}</h3>
        </div>
    <v-alert :value="info" outline type="info" style="color:white" >{{ info }}</v-alert>
    <l-map ref="map" :max-zoom="maxZoom" :zoom="zoom" class="section-map" :style="{height: mapHeight + 'px'}">
      <l-marker v-for="(marker, index) in markers" :lat-lng="marker.position" :key="index+'-marker'" :icon="marker.icon">
        <l-popup v-if="marker.label">
            <div >
              {{marker.label}}
              <v-icon v-if="marker.json" @click="markerInfoClick(marker)" color="info" class="right-btn-icon pointer" :title="$t('sectionsMap.section')">launch</v-icon>
            </div>
          </l-popup>
      </l-marker>
      <!-- <l-polyline v-if="polyLineData" :lat-lngs="polyLineData" :color="routeColor">
        <l-tooltip v-html="routeToolTip"></l-tooltip>
      </l-polyline> -->
      <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
    </l-map>
  </box>
</template>

<script src="./sections-map.js"></script>
<style scoped src="./sections-map.css"></style>

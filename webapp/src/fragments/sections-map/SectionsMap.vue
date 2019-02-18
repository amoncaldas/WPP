<template>
  <box resizable background="white" @boxCreated="boxCreated" @resized="adjustMap" v-if="loaded && markers.length > 0">
    <div slot="header">
          <h3>{{$t('sectionsMap.title')}}</h3>
        </div>
    <v-alert :value="info" outline type="info" style="color:white" >{{ info }}</v-alert>
    <l-map ref="map" :max-zoom="maxZoom" style="z-index:3" :zoom="zoom" class="section-map" :style="{height: mapHeight + 'px'}">
      <l-marker v-for="(marker, index) in markers" :lat-lng="marker.position" :key="index+'-marker'" :icon="marker.icon">
        <l-popup v-if="marker.label">
            <div >
              {{marker.label}}
              <v-icon v-if="marker.json" @click="markerInfoClick(marker)" color="info" class="right-btn-icon pointer" :title="$t('sectionsMap.section')">launch</v-icon>
            </div>
          </l-popup>
      </l-marker>
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

<script src="./sections-map.js"></script>
<style scoped src="./sections-map.css"></style>

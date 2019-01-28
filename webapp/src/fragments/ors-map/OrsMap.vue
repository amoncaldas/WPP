<template>
  <div>
    <v-alert :value="info" outline type="info" style="color:white" >{{ info }}</v-alert>
    <l-map ref="map" :max-zoom="maxZoom" :center="center" :zoom="zoom" :options="mapOptions" class="ors-map" :style="{height: mapHeight + 'px'}">
      <l-geo-json v-if="geoJson" :geojson="geoJson" :options="options"></l-geo-json>
      <l-marker v-for="(marker, index) in markers" :lat-lng="marker.position" :key="index+'-marker'" :icon="marker.icon">
        <l-popup v-if="marker.label">
            <div >
              {{marker.label}}
              <v-icon v-if="marker.json" @click="markerInfoClick(marker)" color="info" class="right-btn-icon pointer" :title="$t('orsMap.locationDetails')">launch</v-icon>
            </div>
          </l-popup>
      </l-marker>
      <template v-if="polygons">
        <l-polygon  v-for="(polygon, index) in polygons"
          :key="index+'-polygon'"
          :lat-lngs="polygon.latlngs"
          :color="polygon.color">
          <l-popup v-if="polygon.label">
            <div >
                {{polygon.label}}
                <v-icon v-if="polygon.json" @click="polygonInfoClick(polygon)" color="info" class="right-btn-icon pointer" :title="$t('orsMap.polygonDetails')">launch</v-icon>
              </div>
          </l-popup>
        </l-polygon>
      </template>

      <l-polyline v-if="polyLineData" :lat-lngs="polyLineData" :color="routeColor">
        <l-tooltip v-html="routeToolTip"></l-tooltip>
      </l-polyline>
       <l-control-layers
        :position="layersPosition"
        :collapsed="false" />
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
  </div>
</template>

<script src="./ors-map.js"></script>
<style scoped src="./ors-map.css"></style>

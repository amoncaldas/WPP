
var map;
var markers = [];
var markerCounter = 0;

function plotMarkers() {
    jQuery(markers).each(function () {
        this.setMap(null);
    });
    markers = [];
    markerCounter = 0;
    var items = jQuery('#travelpoints ul li');
    var latLngList = [];
    var firstPoint = true;
    jQuery(items).each(function () {
        var pointValue = jQuery(this).find('.trajetoValue:first').html();
        var points = pointValue.split('|');
        jQuery(points).each(function (index, value) {

            if (index < 2) {
                if (firstPoint || (!firstPoint && index == 1)) {
                    var lat = value.split(',')[0];
                    var longt = value.split(',')[1];
                    addMarker(new google.maps.LatLng(lat, longt));
                    latLngList.push(new google.maps.LatLng(lat, longt));
                }
            }
        });
        firstPoint = false;

    });
    adjustBounds(latLngList);
}

function addMarker(location) {
    markerCounter++;
    var marker = new google.maps.Marker({
        position: location,
        map: map,
        title: "Ponto " + markerCounter + " do trajeto",
        animation: google.maps.Animation.DROP,
        icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + markerCounter + '|FF0000|000000'
    });
    markers.push(marker);
    
}

function CopyItensToSelect() {
    var selectedPoints = jQuery('.destinosCoordinates')[0];
    jQuery(selectedPoints).html('');
    var items = jQuery('#travelpoints ul li');
    jQuery(items).each(function () {
        var trajetoValue = jQuery(this).find('.trajetoValue:first').html();
        var trajetoShow = jQuery(this).find('div:first').html();
        var pointValue = trajetoValue + '|' + trajetoShow;
        var filteredValue = pointValue.replace('->', '|');
        filteredValue = pointValue.replace('-&gt;', '|');
        jQuery(selectedPoints).html(jQuery(selectedPoints).html() + '<option selected="selected" value="' + filteredValue + '">' + filteredValue + '</option>');
    });
}

function adjustBounds(latLngList) {
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0, ltLgLen = latLngList.length; i < ltLgLen; i++) {
        bounds.extend(latLngList[i]);
    }
    map.fitBounds(bounds);
}

function deleteTrajeto(li) {
    jQuery(li).parent().remove();
    CopyItensToSelect();
    plotMarkers();
}

function AddTrajeto(trajetoData) {
    var trajetoValue = "";
    var trajetoDisplay = "";
    var destinoAbbrv;
    var origemAbbrv;
    var localPartida;
    var localChegada;
    var latitudePartida;
    var longitudePartida;
    var latitudeChegada;
    var longitudeChegada;
    var dataPartida;
    var dataChegada;
    var meioTransporte;

    jQuery(trajetoData).each(function () {
        var $el = jQuery(this);
        var fieldName = $el.prop('name');
        
        if (fieldName == "origemAbbrv") {
            origemAbbrv = $el.val();
        }

        if (fieldName == "destinoAbbrv") {
            destinoAbbrv = $el.val();
        }

        //partida
        if (fieldName == "pods_field_local_de_partida") {
            localPartida = $el.val();
        }
        
        if (fieldName == "pods_field_data_de_partida") {
            dataPartida =  $el.val();
        }
        if (fieldName == "pods_field_latitude_de_partida") {
            latitudePartida = $el.val();
        }
        if (fieldName == "pods_field_longitude_de_partida") {
            longitudePartida = $el.val();
        }

        //transporte
        if (fieldName == "pods_field_transporte") {
            meioTransporte = $el.find(":selected").text();
        }

        //chegada
        if (fieldName == "pods_field_local_de_chegada") {
            localChegada = $el.val();
        }
        if (fieldName == "pods_field_data_de_chegada") {
            dataChegada = $el.val();
        }
        if (fieldName == "pods_field_latitude_de_chegada") {
            latitudeChegada = + $el.val();
        }
        if (fieldName == "pods_field_longitude_de_chegada") {
            longitudeChegada = $el.val();
        }
        
    });

    if (latitudeChegada.toString().length > 0) {
        //+ localPartida + origemAbbrv + "|" + localChegada + destinoAbbrv;
        localPartida = (localPartida.indexOf('(') > 0) ? localPartida : localPartida + origemAbbrv;
        localChegada = (localChegada.indexOf('(') > 0) ? localChegada : localChegada + destinoAbbrv;
        trajetoDisplay = localPartida + " | " + dataPartida + " -> " + localChegada + " | " + dataChegada + " | " + meioTransporte;
        trajetoValue = latitudePartida + "," + longitudePartida + " | " + latitudeChegada + "," + longitudeChegada;
        jQuery('#travelpoints').find("h2").remove();
        var trajetoItem = '<li><div class="trajetoDisplay">' + trajetoDisplay + '</div><div class="trajetoValue">' + trajetoValue + '</div><div onclick="deleteTrajeto(this)" class="deleteTrajeto">x</div></li>';
        jQuery('#travelpoints ul').append(trajetoItem);
        CopyItensToSelect();
        plotMarkers();
    }
    var closeBtn = jQuery('#TB_closeWindowButton');
    jQuery(closeBtn).trigger('click');
}

function adjustAfterReorder() {
    var lastLocation = jQuery("#travelpoints ul li:last", parent.document)[0];
    if (lastLocation != null) {
        jQuery(lastLocation).val();

        var fromlocation = jQuery(lastLocation).find('div:first').html().split('-&gt;');
        fromlocation = fromlocation[1].split('|')[0];
        var latlong = jQuery(lastLocation).find('.trajetoValue').html().split('|')[1];
        var latP = latlong.split(',')[0];
        var longP = latlong.split(',')[1];

        jQuery('.localPartida').val(fromlocation);
        jQuery('.latitudePartida').val(latP);
        jQuery('.longitudePartida').val(longP);

        jQuery('.localPartida').prop('disabled', true);
        jQuery('.latitudePartida').prop('disabled', true);
        jQuery('.longitudePartida').prop('disabled', true);
        var labelValue = jQuery('.pods-form-ui-label-pods-field-local-de-partida').html();
        jQuery('.pods-form-ui-label-pods-field-local-de-partida').html(labelValue + " - a partir do último ponto ");
        jQuery('.pods-form-ui-label-pods-field-local-de-partida').width("230");
    }
}

jQuery(document).ready(function () {

    jQuery('#adicionarTrajeto').click(function () {
        var viagem_id = $('#vid').html();
        tb_show('Definir trajeto', '/wp-content/themes/FAM/admin/trajeto.php?vid=' + viagem_id + '&TB_iframe=true&width=640&height=196');
    });
    

    jQuery('#title').prop('disabled', true);
       

    var initialLat = -9.622414;
    var initialLong = -51.780761;
    var mapOptions = { center: new google.maps.LatLng(initialLat, initialLong), zoom: 3, mapTypeId: google.maps.MapTypeId.ROADMAP, scrollwheel: false };
    var mapElement = document.getElementById('locationmap');
    map = new google.maps.Map(mapElement, mapOptions);
    plotMarkers();
    function initialize() {
        var input = jQuery("#local")[0];
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({ map: map });
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            infowindow.close();
            marker.setVisible(false);
            input.className = '';
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                // Inform the user that the place was not found and return.
                input.className = 'notfound';
                return;
            }
            
             
            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.
            }
            var image = new google.maps.MarkerImage(
		        place.icon,
			    new google.maps.Size(71, 71),
			    new google.maps.Point(0, 0),
			    new google.maps.Point(17, 34),
			    new google.maps.Size(35, 35));
            marker.setIcon(image);
            marker.setPosition(place.geometry.location);

            var address = '';
            if (place.address_components) {
                address = [(place.address_components[0] && place.address_components[0].short_name || ''), (place.address_components[1] && place.address_components[1].short_name || ''), (place.address_components[2] && place.address_components[2].short_name || '')].join(' ');
            }

            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);
            var latlong = place.geometry.location.lat() + ',' + place.geometry.location.lng();
            var selectedPoints = jQuery('.destinosCoordinates')[0];
            jQuery('#travelpoints').find("h2").remove();
            jQuery(selectedPoints).html(jQuery(selectedPoints).html() + '<option selected="selected" value=' + latlong + '>' + latlong + '</option>');
            jQuery('#travelpoints ul').append('<li>' + place.name + ' [' + latlong + ']' + '<div class="trajetoValue">' + latlong + '</div></li>');
            plotMarkers();

        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
    //google.maps.event.addListener(map, 'dragend', function() { alert('map dragged'); } );
});

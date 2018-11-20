jQuery("#latitude").parent().parent().hide();
jQuery("#longitude").parent().parent().hide();
$.mask.definitions['~'] = '(-)?';
jQuery("#temperatura").mask("~9?9");

var initialLat = -9.622414;
var initialLong = -51.780761;

try {
    if (jQuery("#latitude").val() == '' && jQuery("#longitude").val() == '') {
        //navigator.geolocation.getCurrentPosition(GetLocation);
    }
}
catch (ex) { }

function ShowLocation(latitude, longitude, html, localDesc, mapDivId) {

    if (latitude != null && longitude != null && longitude != '' && latitude != '') {

        var mapOptions = {
            center: new google.maps.LatLng(latitude, longitude),
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        };

        gMap = new google.maps.Map(document.getElementById(mapDivId), mapOptions);
        var infowindow = new google.maps.InfoWindow();
        var infowindowsHtml = '';
        infowindowsHtml = html + '<br><div>Local: <strong>' + localDesc + '</strong><br>';
        infowindow.setContent(infowindowsHtml);
        jQuery("#latitude").val(latitude);
        jQuery("#longitude").val(longitude);

    }
}

jQuery(document).ready(function () {
    jQuery("#latitude").keydown(function () {
        if (event.which != 8 && event.which != 46) {
            if (event.which == 13) {
                event.preventDefault();
            }
            else {
                SetPosition(jQuery("#latitude").val(), jQuery("#longitude").val());
            }
        }
    });

    jQuery("#longitude").keydown(function (event) {
        if (event.which != 8 && event.which != 46) {
            if (event.which == 13) {
                event.preventDefault();
            }
            else {
                SetPosition(jQuery("#latitude").val(), jQuery("#longitude").val());
            }
        }
    });
});

function GetLocation(location) {
    initialLat = location.coords.latitude;
    initialLong = location.coords.longitude;
    ShowLocation(initialLat, initialLong, '<span>Localização atual</span>', 'Localização atual', 'locationmap');
}

function initialize() {
   
    if (jQuery("#latitude").val() != '' && jQuery("#longitude").val() != '') {
        initialLat = jQuery("#latitude").val();
        initialLong = jQuery("#longitude").val();
    }
    var mapOptions = {
        center: new google.maps.LatLng(initialLat, initialLong),
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false
    };
    var map = new google.maps.Map(document.getElementById('locationmap'), mapOptions);

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
        var image = new google.maps.MarkerImage( place.icon, new google.maps.Size(71, 71), new google.maps.Point(0, 0), new google.maps.Point(17, 34), new google.maps.Size(35, 35));
        marker.setIcon(image);
        marker.setPosition(place.geometry.location);

        var address = '';
        if (place.address_components) {
            address = [
				  (place.address_components[0] && place.address_components[0].short_name || ''),
				  (place.address_components[1] && place.address_components[1].short_name || ''),
				  (place.address_components[2] && place.address_components[2].short_name || '')
				].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);

        if (place && place.address_components) {
            jQuery(".paisInput").val('');
            jQuery(".cidadeInput").val('');
            var longitude = place.geometry.location.lng();
            var latitude = place.geometry.location.lat();
            jQuery("#latitude").val(latitude);
            jQuery("#longitude").val(longitude);

            ShowLocation(latitude, longitude, '<span>Localização selecionada</span>', 'Local do relato', 'locationmap');

            jQuery(place.address_components).each(function (indexcomp, valuecomp) {
                if (this != null && this.types != null && jQuery(this.types).length > 0) {
                    jQuery(this.types).each(function (index, value) {
                        if (value == 'locality') {
                            jQuery(".cidadeInput").val(place.address_components[indexcomp].long_name);

                        } else {
                            if (value == 'country') {
                                jQuery(".paisInput").val(place.address_components[indexcomp].long_name);
                            }
                        }
                    });
                }
            });

            
        }

       
    });
}

function SetPosition(latitude, longitude) {
    var geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(latitude, longitude);
    geocoder.geocode({ 'latLng': latlng }, function (results1, status1) {
        if (status1 == google.maps.GeocoderStatus.OK) {
            if (results1[0]) {
                var locationData = results1[0];
                jQuery(locationData.address_components).each(function (indexcomp, valuecomp) {
                    if (this != null && this.types != null && jQuery(this.types).length > 0) {
                        jQuery(this.types).each(function (index, value) {
                            if (value == 'locality') {
                                jQuery(".cidadeInput").val(locationData.address_components[indexcomp].long_name);
                            } else {
                                if (value == 'country') {
                                    jQuery(".paisInput").val(locationData.address_components[indexcomp].long_name);

                                }
                            }
                        });
                    }
                });
            }
        }
    });
}

google.maps.event.addDomListener(window, 'load', initialize);

//google.maps.event.addListener(map, 'click', function (event) {

//    marker = new google.maps.Marker({ position: event.latLng, map: map });

//});


function ShowLocation(latitude, longitude, html, localDesc, mapDivId) {

    if (latitude != null && longitude != null && longitude != '' && latitude != '') {

        var mapOptions = {
            center: new google.maps.LatLng(latitude, longitude),
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        };

        gMap = new google.maps.Map(document.getElementById(mapDivId), mapOptions);


        var infowindow = new google.maps.InfoWindow();
        var infowindowsHtml = '';
        infowindowsHtml = html + '<br><div>Local: <strong>' + localDesc + '</strong><br>';
        infowindow.setContent(infowindowsHtml);
        //infowindow.open(map, marker);

    }
}
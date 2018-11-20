jQuery('.pods-form-fields').append("<li><input name='destinoAbbrv' id='destinoAbbrv' type='hidden'/></li><li><input name='origemAbbrv' id='origemAbbrv' type='hidden'/></li>");

var lastDate = "01/01/1970";
var originAbbrev = '';
jQuery(document).ready(function () {  

    jQuery(".dataPartida, .dataChegada").val('');
    jQuery(".dataPartida, .dataChegada").attr('maxlength', '19');
    jQuery(".dataPartida, .dataChegada").keyup(function (e) {
        var max = 16;
        if (e.keyCode != 8) {
            if (jQuery(this).val().length == 2) {
                jQuery(this).val(jQuery(this).val() + "/");
            }
            else if (jQuery(this).val().length == 5) {
                jQuery(this).val(jQuery(this).val() + "/");
            }
            else if (jQuery(this).val().length == 10) {
                jQuery(this).val(jQuery(this).val() + " ");
            }
            else if (jQuery(this).val().length == 13) {
                jQuery(this).val(jQuery(this).val() + ":");
            }
            else if (jQuery(this).val().length == 16) {
                jQuery('#ui-datepicker-div').hide();
            }
            jQuery(this).val(jQuery(this).val().substr(0, max));

                     
        }
    });
    

    var lastLocation = jQuery("#travelpoints ul li:last", parent.document)[0];
    if (jQuery(lastLocation).find('div:first').html() != null) {
        lastDate = jQuery(lastLocation).find('div:first').html().split('|')[2];
    }
    if (lastLocation != null) {
        jQuery(lastLocation).val();

        var fromlocation = jQuery(lastLocation).find('div:first').html().split('-&gt;');
        fromlocation = fromlocation[1].split('|')[0];
        var trajetoValueArray = jQuery(lastLocation).find('.trajetoValue').html().split('|');
        var latlong = trajetoValueArray[1];
        originAbbrev = trajetoValueArray[2];
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

    function initializeTrajeto() {
        var origem = jQuery("#pods-form-ui-pods-field-local-de-partida")[0];
        var autocompleteOrigem = new google.maps.places.Autocomplete(origem);

        var localPartida = jQuery('.localPartida').val();
        var regex = /\/embed\/(.*?)\?/;
        var m = regex.exec(localPartida);
        if (m != null) {
            localPartida = localPartida.replace(m[0],"");
        }
        //var origemAbbrv =  GetCountryAbbreviation( jQuery('.latitudePartida').val(), jQuery('.longitudePartida').val() ) ;
        jQuery('#origemAbbrv').val('(' + originAbbrev + ')');
        
        //get origem information
        google.maps.event.addListener(autocompleteOrigem, 'place_changed', function () {
            origem.className = '';
            var placeOrigem = autocompleteOrigem.getPlace();      

            if (!placeOrigem.geometry) {
                // Inform the user that the place was not found and return.
                origem.className = 'notfound';
                return;
            }           

            if (placeOrigem.address_components) {               
                jQuery('#origemAbbrv').val('(' + GetCountryAbbrev(placeOrigem.address_components) + ')');
                jQuery(".cidadeInput").val('');                
                jQuery('#pods-form-ui-pods-field-latitude-de-partida').val(placeOrigem.geometry.location.lat());
                jQuery('#pods-form-ui-pods-field-longitude-de-partida').val(placeOrigem.geometry.location.lng());
            }
        });

        //get destino information
        var destino = jQuery("#pods-form-ui-pods-field-local-de-chegada")[0];
        var autocompleteDestino = new google.maps.places.Autocomplete(destino);

        google.maps.event.addListener(autocompleteDestino, 'place_changed', function () {
            destino.className = '';
            var placeDestino = autocompleteDestino.getPlace();
            if (!placeDestino.geometry) {
                // Inform the user that the place was not found and return.
                destino.className = 'notfound';
                return;
            }        

            if (placeDestino.address_components) {
              
                jQuery('#destinoAbbrv').val(' (' + GetCountryAbbrev(placeDestino.address_components) + ')');
                jQuery('#pods-form-ui-pods-field-latitude-de-chegada').val(placeDestino.geometry.location.lat());
                jQuery('#pods-form-ui-pods-field-longitude-de-chegada').val(placeDestino.geometry.location.lng());
            }
        });
    }
    google.maps.event.addDomListener(window, 'load', initializeTrajeto);

    jQuery('#saveTrajeto').click(function () {

        var postdata = {};
        var validForm = true;

        var $submittable = jQuery('form.pods-submittable');
        $submittable.find('.pods-submittable-fields').find('input, select, textarea').each(function () {
            var $el = jQuery(this);
            var fieldName = $el.prop('name');

            if ('' != fieldName && 0 != fieldName.indexOf('field_data[')) {
                var val = $el.val();

                if ($el.is('input[type=checkbox]') && !$el.is(':checked'))
                    val = 0;
                else if ($el.is('input[type=radio]') && !$el.is(':checked'))
                    return true; // This input is not checked, continue the loop
                if (($el.is(':visible') && $el.is("select"))) {                    
                    val = $el.find(":selected").text();
                }
                else {
                    $el.parent().find('.pods-validate-error-message').remove();
                    $el.removeClass('pods-validate-error');
                }

                postdata[fieldName] = val;
            }
        });
        if (validForm) {            
            var trajetoData = $submittable.find('.pods-submittable-fields').find('input, select, textarea');
            window.parent.AddTrajeto(trajetoData);
        }

    });
});

function GetCountryAbbreviation(lat, long) {
    var geocoder = new google.maps.Geocoder();
    var countryAbbr = false;
    var lat = parseFloat(lat);
    var lng = parseFloat(long);
    var latlng = new google.maps.LatLng(lat, lng);
    var returnValue = '';
    geocoder.geocode({ 'latLng': latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                var locationAddress = results[1];
                jQuery(locationAddress).each(function (index, value) {
                    var info = value;
                    for (var i = 0, len = info.types.length; i < len; ++i) {
                        var type = info.types[i];
                        if (type == 'country') {
                            var componenteAbbr = info.short_name;
                            if (componenteAbbr != null && componenteAbbr.length == 2) {
                                returnValue = returnValue + "country:{" + info.short_name + '|' + info.long_name + "};";
                            }
                        }
                        if (type == 'administrative_area_level_1') {
                            var componenteAbbr = info.short_name;
                            if (componenteAbbr != null) {
                                returnValue = returnValue + "state:{" + info.short_name + '|' + info.long_name + "};";
                            }
                        }
                    }
                });
            }
        }
    });
    returnValue = returnValue.split(';')[1].split(':')[1].split('|')[0].replace('{', '');//fixit
    return returnValue;
}

function GetCountryAbbrev(adressComponentes) {
    var returnValue = '';
    var location = adressComponentes;
    jQuery(adressComponentes).each(function (index, value) {
        var info = value;
        
        for (var i = 0, len = info.types.length; i < len; ++i) {
            var type = info.types[i];
            if (type == 'country') {
                var componenteAbbr = info.short_name;
                if (componenteAbbr != null && componenteAbbr.length == 2) {                   
                    returnValue = returnValue + "country:{" + info.short_name + '|' + info.long_name + "};";
                }
            }
            if (type == 'administrative_area_level_1') {
                var componenteAbbr = info.short_name;
                if (componenteAbbr != null) {
                    returnValue = returnValue + "state:{" + info.short_name + '|' + info.long_name + "};";
                }
            }
        }
        
    });
    returnValue = returnValue.split(';')[1].split(':')[1].split('|')[0].replace('{', '');//fixit
    return returnValue;
}


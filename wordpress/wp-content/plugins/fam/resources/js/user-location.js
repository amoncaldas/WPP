if (typeof ($) === 'undefined') {
    var $ = jQuery.noConflict();
};
var _listeningUserLocationActive = false;
$(document).ready(function () {
    
    $(".localNascimento").live("focus", function () {
        if (_listeningUserLocationActive == false) {
            _listeningUserLocationActive = true;
            google.maps.event.addDomListener(window, 'click', initializeLocalNascimentoSuggestion);
        }
     });

    $(".localResidencia").live("focus", function () {
        if (_listeningUserLocationActive == false) {
            _listeningUserLocationActive = true;
            google.maps.event.addDomListener(window, 'click', initializeLocalResidenciaSuggestion);
        }
     });
    
});

function initializeLocalNascimentoSuggestion() {
    var origem = $(".localNascimento")[0];
    if (origem != null && origem != 'undefined') {
        var autocompleteOrigem = new google.maps.places.Autocomplete(origem);

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
                $('.latitudeNascimento').val(placeOrigem.geometry.location.lat());
                $('.longitudeNascimento').val(placeOrigem.geometry.location.lng());
            }
        });
    }
}

function initializeLocalResidenciaSuggestion() {
    var origem = $(".localResidencia")[0];
    if (origem != null && origem != 'undefined') {
        var autocompleteOrigem = new google.maps.places.Autocomplete(origem);

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
                $('.latitudeResidencia').val(placeOrigem.geometry.location.lat());
                $('.longitudeResidencia').val(placeOrigem.geometry.location.lng());
            }
        });
    }
}
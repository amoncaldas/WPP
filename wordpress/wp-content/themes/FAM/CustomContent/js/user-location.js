var $jQ;
if (jQuery != null) {
    $jQ = jQuery;
} else {
    $jQ = $;
}
$jQ(document).ready(function () {
    $jQ(".localNascimento").live("focus", function () {
        google.maps.event.addDomListener(window, 'keydown', initializeLocalNascimentoSuggestion);
    });

    $jQ(".localResidencia").live("focus", function () {
        google.maps.event.addDomListener(window, 'keydown', initializeLocalResidenciaSuggestion);
    });

    $jQ("body").live("mousemove", function () {
        google.maps.event.addDomListener(window, 'mousemove', initializeLocalNascimentoSuggestion);
        google.maps.event.addDomListener(window, 'mousemove', initializeLocalResidenciaSuggestion);
    });

    //$jQ('.pods-meta tr').eq(4).hide();
    //$jQ('.pods-meta tr').eq(5).hide();
    //$jQ('.pods-meta tr').eq(7).hide();
    //$jQ('.pods-meta tr').eq(8).hide();
    
});




function initializeLocalNascimentoSuggestion() {
    var origem = $jQ(".localNascimento")[0];
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
                $jQ('.latitudeNascimento').val(placeOrigem.geometry.location.lat());
                $jQ('.longitudeNascimento').val(placeOrigem.geometry.location.lng());
            }
        });
    }
}

function initializeLocalResidenciaSuggestion() {
    var origem = $jQ(".localResidencia")[0];
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
                $jQ('.latitudeResidencia').val(placeOrigem.geometry.location.lat());
                $jQ('.longitudeResidencia').val(placeOrigem.geometry.location.lng());
            }
        });
    }
}

var ListenerMediaAdded = false;
var _setMediaLocationControl_timeout;

jQuery(window).load(function ($) {

    try {
        if (uploader == 'undefined');

        uploader.settings.max_file_size = '200097152b';
        uploader.settings['resize'] = { width: 1024, height: 1024, quality: 75 };

        //uploader.settings.max_file_count = 2;
        //uploader.bind('FilesAdded', function (up, files) {
        //    var i = up.files.length,
        //        maxCountError = false;

        //    plupload.each(files, function (file) {

        //        if (uploader.settings.max_file_count && i >= uploader.settings.max_file_count) {
        //            maxCountError = true;
        //            setTimeout(function () { up.removeFile(file); }, 50);
        //        } else {
        //            // Code to add pending file details, if you want
        //        }

        //        i++;
        //    });

        //    if (maxCountError) {
        //        // Too many files uploaded, do something
        //    }

        //});

    } catch (err) { }

});

function CheckMediaLoaded() {
    clearTimeout(_setMediaLocationControl_timeout);   
    _setMediaLocationControl_timeout =  window.setTimeout("SetMediaLocationControl()", 100);    
}

function SetMediaLocationControl() {
    var scriptContainer = jQuery('#media_location_script_container');
    var containerParents = jQuery(scriptContainer).parents();
    if (jQuery(containerParents).find('div.media-sidebar').length == 0) {
        if (scriptContainer.length > 0) {
            var scriptTr = jQuery(scriptContainer).parent().parent();

            if (scriptTr.length == 0) {                
                clearTimeout(_setMediaLocationControl_timeout);
                _setMediaLocationControl_timeout = window.setTimeout("SetMediaLocationControl()", 500);
            }
            else {
                jQuery('head').append("<style>.media-item .pinkynail {max-width:119px !important;max-height:69px !important;} #media-items .media-item{min-height:70px !important}</style>");
                jQuery('[class$=attachment_location]').find('input').css('width', '100%');
                jQuery('[class$=attachment_latitude]').find('input').css('width', '100%');
                jQuery('[class$=attachment_longitude]').find('input').css('width', '100%');
                jQuery(scriptTr).hide();
                google.maps.event.addDomListener(window, 'click', initializeSuggestion);
            }
        }
    }
    else {
        jQuery('[class$=attachment_location]').parent().parent().hide();
        jQuery('[class$=attachment_latitude]').parent().parent().hide();
        jQuery('[class$=attachment_longitude]').parent().parent().hide();
    }
}



function initializeSuggestion() {
    var origem = jQuery('[class$=attachment_location]').find('input')[0];
    if (origem != null && origem != 'undefined') {
        var autocompleteOrigem = new google.maps.places.Autocomplete(origem);
        ListenerMediaAdded = true;
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
                jQuery('[class$=attachment_latitude]').find('input').val(placeOrigem.geometry.location.lat());
                jQuery('[class$=attachment_longitude]').find('input').val(placeOrigem.geometry.location.lng());

               
            }
        });
    }
}
var cropped = false;
var cropWidth = 985;
var cropHeight = 350;
var imgHeight;
var imgWidth;
var api;
var loadinggifurl = '/wp-admin/images/loading.gif';

function SelectForCrop(imgId, imgSrc) {
    window.parent.setImageForCrop(imgId, imgSrc);
}

(function ($) {
    var origAppend = $.fn.append;
    $.fn.append = function () {
        return origAppend.apply(this, arguments).trigger("append");
    };
})(jQuery);

jQuery("#media-items").bind("append", function () {    
    jQuery("#save-all").val('sel. p/ corte');
    jQuery("#save").val('sel. p/ corte');
    
});

jQuery('.submitdelete').click(function (e) {
    var deletedestaque = confirm('A imagem recortada será excluída');
    if (deletedestaque === true) {
        DeleteImageOnUnload();
    }
    else {
        e.preventDefault();
    }
    
});

jQuery("#save-all").live("click", function (e) {
    e.preventDefault();
    var info = jQuery("#media-items").find('.media-item-info');
    if (info.length > 0) {
        var mediaUploaded = jQuery('#media-items .media-item').first();
        var the_id = jQuery(mediaUploaded).attr('id');
        var media_id_split = the_id.split('-');
        var imgSrc = jQuery(mediaUploaded).find('img').attr('src').replace('-119x69', '');
        var imgSrc = imgSrc.replace('-120x70', '');
        selectImage(media_id_split[2], imgSrc);
    }
});



jQuery("#save").live("click", function (e) {
    e.preventDefault();
    var info = jQuery("#media-items").find('.media-item-info');
    if (info.length > 0) {
        checkCoords();
        var mediaUploaded = jQuery('#media-items .media-item .open').first();
        var the_id = jQuery(mediaUploaded).attr('id');
        var media_id_split = the_id.split('-');
        var imgSrc = jQuery(mediaUploaded).find('img').attr('src').replace('-119x69', '');
        var imgSrc = imgSrc.replace('-120x70', '');
        selectImage(media_id_split[2], imgSrc);
    }
});


function CreateCropSelectBtn(container) {
    var hiddenId = jQuery(container).find('[id^="type-of-"]');
    var the_id = jQuery(hiddenId).attr('id');
    var media_id_split = the_id.split('-');    
    var imgSrc = jQuery(container).find('img').attr('src').replace('-119x69', '');
    var imgSrc = imgSrc.replace('-120x70', '');
    var root = location.pathname.split('wp-admin')[0];   
    jQuery(container).find('.filename:last').after('<div style="float: right;width: 71px;height:20px;padding-top: 10px;margin-top: -37px;margin-right: 70px;" class="select-box"><label class="crop_btn" style="color:#21759b; cursor:pointer;text-decoration: underline;" onclick="SelectForCrop(' + media_id_split[2] + ', ' + "'" + imgSrc + "'" + ')" >sel. p/ cortar</label></div>');
    jQuery('#filter').append('<input type="hidden" name="selection" value="crop" />');
    $(container).find('.menu_order_input').remove();
}

jQuery(document).ready(function () {
    jQuery('#remove-image').hide();
    if (window.location.href.toString().indexOf('selection=crop') > -1) {
        if ($('#filter').find('input[name=selection]').length == 0) {
            $('#filter').append('<input type="hidden" name="selection" value="crop">');
        }
        jQuery('#media-items .media-item').each(function () {
            var mediaItem = this;
            CreateCropSelectBtn(mediaItem);
        });
    }

    var actionform = jQuery('#media-upload form');
    var action = jQuery(actionform).attr("action");
    if(action == null || action == '')
    {
        action = location.href;
    }
    jQuery(actionform).attr("action", action + "&selection=crop");

});

function setImageForCrop(imgId, imgSrc) { 
    cropped = false;
    
    if (typeof (imgSrc) != 'undefined' && typeof (imgId) != 'undefined')
    {
        tb_remove();
        jQuery('#tooSmall').remove();
        jQuery('#upload_image_id').val(imgId);
        var filename = imgSrc.substring(imgSrc.lastIndexOf('/') + 1);
        jQuery('#upload_image_src').val(filename);
        jQuery('#imageToCrop').find('#tocrop').parent().remove();
        jQuery('#handw').show();
        jQuery('#remove-image').show();
        jQuery('#set-image').hide();
        jQuery('#imageToCrop').append('<img style="max-width:100%;" id="tocrop" src="' + imgSrc + '" />');
        var imgToCrop = new Image();

        var img_tag = $('#tocrop');
        var img_tag_type = typeof (img_tag);
        if (imgSrc.length > 5) {
            $('#tocrop').error(function () {
                $('#tooSmall').remove();
                $('#tocrop').before('<span  id="tooSmall" >O arquivo de imagem salvo anteriormente não existe mais. Selecione outra imagem.</span>');
                $('#tocrop').remove();
                no_img_error = false;
                $('#remove-image').hide();
                $('#set-image').show();
                $('#handw').hide();

            });

            
            var src_to_crop = imgSrc.split('?randon=')[0] + '?randon=' + Math.floor(Math.random() * (999999 - 10 + 1) + 999999);
            
            try {
                imgToCrop.src = src_to_crop;
            }
            catch (ex) { }
            
        }
        if (imgSrc == null || imgSrc.length < 5) {
            $('#handw').hide();
            $('#remove-image').hide();
            $('#set-image').show();
           
        }
        
       

        jQuery(imgToCrop).load(function () {
            imgHeight = this.height;
            imgWidth = this.width;
            
            jQuery('#tooSmall').remove();
            if ((imgHeight == cropHeight || imgHeight + 1 == cropHeight) && (imgWidth == cropWidth || imgWidth + 1 == cropWidth)) {
                cropped = true;
                jQuery('#tocrop').before('<span  id="tooSmall" >Imagem já recortada. Você pode excluir essa imagem e selecionar outra ou salvar as alterações.</span>');
                jQuery('#sendCrop').hide();
                jQuery('#dimensionsToCrop').hide();
                jQuery('#handw').hide();
            }
            else {
                if (imgHeight >= cropHeight && imgWidth >= cropWidth) {
                    api = jQuery.Jcrop('#tocrop', {
                        onChange: updateCoords,
                        onSelect: updateCoords,
                        allowResize: false,
                        allowSelect: true,
                        allowMove: true,
                        aspectRatio: cropWidth / cropHeight,
                        boxWidth: 850,
                        boxHeight: 700,
                        trueSize: [imgWidth, imgHeight],

                    });
                    jQuery('#sendCrop').show();
                    $('#remove-image').show();
                    $('#set-image').hide();
                    api.setSelect([0, 0, cropWidth, cropHeight]);
                    //api.animateTo([0, 0, cropWidth, cropHeight]);
                    window.setTimeout(function () { }, 500)
                    {
                        jQuery('#dimensionsToCrop').show();
                        
                    }

                    var isCtrl = false;
                    jQuery(document).keyup(function (e) {
                        api.setOptions({ aspectRatio: 0 });
                        jQuery(api).focus();
                        if (e.which == 17) isCtrl = false;
                    }).keydown(function (e) {
                        if (e.which == 17) isCtrl = true;
                        if (e.which == 81 && isCtrl == true) {
                            api.setOptions({ aspectRatio: 1 });
                            api.focus();
                        }
                    });
                    if (getParameterByName('message') != '1') {
                        window.onbeforeunload = function () {
                            return 'Você selecionou a imagem mas não salvou o destaque. Se abandonar essa página o destaque não será salvo';
                        };
                    }
                }
                else {
                    if (cropped) {
                        jQuery('#tocrop').before('<span  id="tooSmall" >Imagem já recortada. Você pode excluir essa imagem e selecionar outra ou salvar as alterações.</span>');
                    }
                    else if (!cropped) {
                        jQuery('#tocrop').before('<span  id="tooSmall" >A imagem  deve ter no mínimo ' + cropWidth + ' x ' + cropHeight + ' para ser recortada, mas tem ' + imgWidth + ' x ' + imgHeight + '. Selecione outra imagem</span>');
                    }
                    jQuery('#sendCrop').hide();
                    jQuery('#dimensionsToCrop').hide();
                    jQuery('#handw').hide();
                }
            }
            jQuery('.jcrop-holder').css('margin-left', '-10px');
        });

        
    }
}

function updateCoords(c) {
    jQuery('#handw').show();
    jQuery('#x').val(c.x);
    jQuery('#y').val(c.y);
    jQuery('#w').val(c.w);
    jQuery('#h').val(c.h);
    jQuery('#pich').html(c.h);
    jQuery('#picw').html(c.w);
};

function checkCoords() {
    if (parseInt(jQuery('#x').val())) {
        return true;
    }
    else {
        //alert('Selecione uma área para recorte!');
    }
    return false;
};

jQuery(document).ready(function () {    

    jQuery("#title").keyup(function (e) {
        var max = 25;
        if (e.keyCode != 8) {            
            jQuery(this).val(jQuery(this).val().substr(0, max));
            if (jQuery(this).val().length == max) {
                alert('São permitidos no máximo ' + max + ' caracteres na descrição');
            }            
        }
    });

    jQuery('#dimensionsToCrop').append( '<span>' + cropWidth + ' x ' + cropHeight + '</span>');
    jQuery('#dimensionsToCrop').hide();
    var editImgSrc = jQuery('#upload_image_src').val();
       
    setImageForCrop(jQuery('#upload_image_id').val(), editImgSrc);
   
   jQuery('#set-image').click(function () {
       tb_show('Selecione a imagem para recortar', 'media-upload.php?post_id=' + postId + '&amp;selection=crop&amp;type=image&amp;TB_iframe=true');
        return false;
   });

   jQuery('#publish').click(function (e) {       
       if (!cropped) {
           if (imgHeight != cropWidth || cropWidth != imgWidth) {
               e.preventDefault();
               alert('Você deve selecionar uma imagem e recortá-la antes de publicar');
               e.preventDefault();
               return false;
           }
       }
       else {
           window.onbeforeunload = null;
           window.onunload = null;
       }
   });   

   jQuery('#remove-image').click(function () {
        cropped = false;
        var btn = $(this);
        window.onbeforeunload = null;
        window.onunload = null;
        $(btn).css('background', 'url(' + loadinggifurl + ') #fff no-repeat center center');
        $(btn).html('');
        jQuery('#upload_image_id').val('');
        jQuery('#imageToCrop').find('img').remove();
        //jQuery('#set-image').show();
        jQuery('#handw').hide();
        jQuery('#sendCrop').hide();
        //jQuery('#remove-image').hide();
        jQuery('#tooSmall').remove();
        jQuery('#dimensionsToCrop').hide();
        jQuery('.jcrop-holder').remove();
        
        var img_src = jQuery('#upload_image_src').val().split('?randon=')[0];
        jQuery.ajax({
            
            url: baseUrl + '/wp-content/themes/FAM/CustomContent/destaque.php',
            type: 'post',
            dataType: 'html',
            data: {                
                'blogId': jQuery('#blogId').val(),
                'delete_image_src': img_src,
                'action_delete_crop': 'true'
            },
            success: function (data) {
                if (data.length > 0) {
                    if (data == "success") {
                        var imgId = null;
                        imgId = jQuery('#upload_image_id').val();
                        if (imgId > 0) {
                            //setImageForCrop(imgId, data);
                        }
                        jQuery('#set-image').show();
                        jQuery('#remove-image').hide()
                        
                        $(btn).css('background', '#21759B');
                        $(btn).html('Remover imagem');
                    }
                    else {
                        $(btn).css('background', '#21759B');
                        jQuery('#set-image').show();
                        jQuery('#remove-image').hide();
                        $(btn).html('Escolher imagem');
                    }
                }
                else {
                    $(btn).css('background', '#21759B');
                    jQuery('#set-image').show();
                    jQuery('#remove-image').hide();
                    $(btn).html('Escolher imagem');
                }
            },
            error: function (request, error) {
                alert("Desculpe, ocorreu um erro ao excluir a imagem" + error);
                $(btn).css('background', '#21759B');
            }
    });


        return false;
   });

   jQuery('#sendCrop').click(function (e) {
       var btn = $(this);
       
       var upload_image_src = jQuery('#tocrop').attr('src').split('?randon=')[0];
       jQuery('#originalSrc').val(upload_image_src);
       var selectedWidth =  parseInt(jQuery('#w').val());
       var selectedHeight = parseInt(jQuery('#h').val());
       if (selectedHeight >= cropHeight && selectedWidth >= cropWidth) {
           $(btn).css('background', 'url(' + loadinggifurl + ') #fff no-repeat center center');
           $(btn).html('');
           e.preventDefault();
           checkCoords();           
           jQuery.ajax({
               url: baseUrl + '/wp-content/themes/FAM/CustomContent/destaque.php',
               type: 'post',
               dataType: 'html',
               data: {
                   'x': jQuery('#x').val(),
                   'y': jQuery('#y').val(),
                   'w': jQuery('#w').val(),
                   'h': jQuery('#h').val(),
                   'blogId': jQuery('#blogId').val(),
                   'upload_image_src': upload_image_src,
                   'upload_image_id': jQuery('#upload_image_id').val(),
                   'action_crop': 'true'

               },
               success: function (data) {
                   if (data.length > 0) {
                       if (data != "error") {
                           var imgId = jQuery('#upload_image_id').val();
                           $(btn).html('Recortar');
                           $(btn).css('background', '#21759B');
                           setImageForCrop(imgId, 'files/cropped_destaque/' + data);
                           cropped = true;
                           window.onbeforeunload = function () {                              
                               return 'Você recortou a imagem mas não salvou o destaque. Se abandonar essa página o destaque não será salvo';                               
                           };
                           window.onunload = function () {
                               DeleteImageOnUnload();
                           }
                       }
                       else {
                           alert("Desculpe, ocorreu um erro:" + data);
                       }
                   }
                   else {
                       alert("Desculpe, ocorreu um erro:" + data);
                   }
               },
               error: function (request, error) {
                   alert("Desculpe, ocorreu um erro ao executar o recorte:" + error);
               }
           });
       }
       else {
           alert("A área selecionada para recorte deve ter no mínimo " + cropWidth + " x " + cropHeight);
       }
    });
    
});

function DeleteImageOnUnload() {          
    jQuery.ajax({
        url: baseUrl + '/wp-content/themes/FAM/CustomContent/destaque.php',
        type: 'post',
        dataType: 'html',
        async: false,
        data: {
            'blogId': jQuery('#blogId').val(),
            'delete_image_src': jQuery('#upload_image_src').val(),
            'action_delete_crop': 'true'
        },
        success: function (data) {
            if (data.length > 0) {
                if (data != "error") {
                    log('imagem recortada excluída');
                }
                else {
                    log('imagem recortada não excluída');
                }
            }
        }
    });
}
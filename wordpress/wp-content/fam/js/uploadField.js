
var _upload_single;
var _upload_mandatory = false;
var _postId;
var _checkMediaAdded_time_out;

function selectAsUploadedImg(imgId, imgSrc, element) {
    var container;
    if (jQuery(element).hasClass('selectbtn')) {
        container = jQuery(element).parent().parent();
        btn = jQuery(container).find('.selectbtn');
        if (jQuery(btn).html() != 'Ok') {
            jQuery(btn).html('Ok');
            var fileName = jQuery(container).find('.filename .title').html().replace("-120x70", "-190x140");
            imgSrc = imgSrc.replace("-120x70", "-190x140");
            var upId = getParameterByName('uploadId');
            window.parent.setSelectedImage(imgId, imgSrc, fileName, upId);
        }
    }
    else {
        container = element;
        var fileName = jQuery(container).find('.filename .title').html();
        var upId = getParameterByName('uploadId');
        window.parent.setSelectedImage(imgId, imgSrc, fileName, upId);
    }
   
    
}

function OpenMediaManager(postId, uploadId, disableYoutubeVideo) {
   
    var youtube = (disableYoutubeVideo) ? 'disableyoutube=yes&amp;' : '&amp;';   
    if (postId != null && postId != 0) {
        tb_show('Seleção de mídia', 'media-upload.php?post_id=' + postId + '&amp;selection=famupload&amp;uploadId=' + uploadId + '&amp;' + youtube + 'TB_iframe=true');
    }
    else {
        tb_show('Seleção de mídia', 'media-upload.php?selection=famupload&amp;uploadId=' + uploadId + '&amp;' + youtube + 'TB_iframe=true');
        
    }
}

function CreateLibraryBtn(container) {
    var hiddenId = jQuery(container).find('[id^="type-of-"]');
    var the_id = jQuery(hiddenId).attr('id');
    var media_id_split = the_id.split('-');
    var imgSrc = jQuery(container).find('img').attr('src');//.replace('-119x69', '');
    var root = location.pathname.split('wp-admin')[0];
    var filename = jQuery(container).find('.filename:last');
    if (jQuery(filename).find('.selectbtn').length == 0) {
        jQuery(container).find('.filename:last').after('<div style="float: right;width: 64px;height:20px;padding-top: 10px;margin-top: -37px;margin-right: 70px;" class="select-box"><label class="selectbtn" style="color:#21759b; cursor:pointer;text-decoration: underline;" onclick="selectAsUploadedImg(' + media_id_split[2] + ', ' + "'" + imgSrc + "'" + ',this)" >Selecionar</label></div>');
        if (jQuery('#tab-type a').hasClass('current')) {
            var parentUploader = jQuery(".fam_upload .upload_single", parent.document);
            if ( jQuery(parentUploader).length == 0 || jQuery(parentUploader).val() != 'true') {
                jQuery(container).find('.selectbtn').trigger('click');
            }
        }
    }
    jQuery('#filter').find("#famupload_filter").remove();
    jQuery('#filter').append('<input type="hidden" id="famupload_filter" name="selection" value="famupload" />');

    jQuery('#filter').find("#famupload_id").remove();
    var upId = getParameterByName('uploadId');
    if (upId != null) {
        jQuery('#filter').append('<input type="hidden" id="famupload_id" name="uploadId" value="' + upId + '" />');
    }
}

function HandleSaveLibrarySaveBtn(btnid) {
    //var mediaContainer = jQuery(btnid).parents().find('#media-items');
    //var mediaUploaded = jQuery(mediaContainer).find('.media-item').first();
    //var idHidden = jQuery(mediaUploaded).find('input[type=hidden]');
    //var the_id = jQuery(idHidden).attr('id');
    //var media_id_split = the_id.split('-');
    //var imgSrc = jQuery(mediaUploaded).find('img').attr('src');
    //selectAsUploadedImg(media_id_split[2], imgSrc, mediaContainer);
    try
    {
        window.parent.tb_remove();
    }
    catch(e)
    {
    }
}

function setSelectedImage(imgId, imgSrc, fileName, uploadId) {
    
    enlargeHref = imgSrc.replace("-190x140", "");
    if(imgSrc.indexOf('crystal/video.png') > 0)//if is video
    {     
        imgSrc = GetYoutubeVideo(imgId, location.href);
        var videoCode = imgSrc.replace('http://img.youtube.com/vi/', '').replace('1.jpg', '');
        enlargeHref = "http://www.youtube.com/embed/" + videoCode;
    }
   
    var containerSelector = '.fam_upload input[value=' + uploadId + ']';
    var uploadContainer = jQuery(containerSelector).parent();

    var hasUl = jQuery(uploadContainer).has('ul');
    if (!hasUl.length) {
        jQuery(uploadContainer).append('<ul></ul>');
    }
    var site_id = GetCurrentBlogId(location.href);
    var uploadPrefix = GetUploadPrefix(uploadContainer);
    jQuery(uploadContainer).find('ul').append('\
        <li>\
            <span class="mover"></span>\
            <a class="fancybox fancybox.iframe" href="' + enlargeHref + '" rel="permalink" title="' + fileName + '">\
            <img src="' + imgSrc + '" /></a>\
            <input type="hidden" value="' + imgId + '" id="' + uploadPrefix + '_fam_upload_id_' + imgId + '" name="' + uploadPrefix + '_fam_upload_id_' + imgId + '"/>\
            <input type="hidden" value="' + site_id + '" id="' + uploadPrefix + '_fam_upload_site_id_' + imgId + '" name="' + uploadPrefix + '_fam_upload_site_id_' + imgId + '"/>\
            <input type="text" value="' + fileName + '" name="' + uploadPrefix + '_fam_upload_name_' + imgId + '" id="' + uploadPrefix + '_fam_upload_name_' + imgId + '" />\
            <span title="Remover item" class="fam_delete_img"><span>\
            <span class="cover_label">É capa</span>\
            <input  value="cover" name="_fam_upload_is_cover_' + imgId + '" class="fam_is_cover" id="_fam_upload_is_cover_' + imgId + '" type="checkbox">\
        <li>');
    jQuery(uploadContainer).find('ul li').each(function () {
        var liContent = jQuery(this).html();
        if (liContent.length == 0) {
            jQuery(this).remove();
        }
    });
    
    if (UploadIsSingle(uploadContainer)) {
        $(uploadContainer).find('ul li .mover').hide();        

        if (Is_pupload(uploadContainer)) {
           $(uploadContainer).parents('.plupload-upload-uic').find('input[type=button]').hide();
        }
        else {
            tb_remove();
        }
        jQuery(uploadContainer).find('ul li .mover').remove();
        jQuery(uploadContainer).find('a.upload_btn_item').remove();
        jQuery(uploadContainer).find('.description').remove();

    }
    else {        
        var itens = $(uploadContainer).find('ul li');
        if (itens.length == 1 || is_mobile) {
            $(uploadContainer).find('ul li .mover').hide();
        }
        else {
            $(uploadContainer).find('ul li .mover').show();
            jQuery(uploadContainer).find('ul').sortable();
        }
        
    }
    $('.fancybox').fancybox();
}

function getParameterByName(name) {
    var url = location.search.replace('?', '');
    var parans = url.split('&');
    for (var i = 0; i < parans.length; i++) {
        selectedParam = parans[i].split('=');
        if (selectedParam[0] == name) {
            return selectedParam[1];
        }
    }
    return '';    
}

function UploadIsSingle(container)
{
    var singleVal = jQuery(container).find('.upload_single').val();
    return (singleVal == 'true');
}

function DisableYoutubeVideo(container) {
    var youtubeVal = jQuery(container).find('.disable_youtube_video').val();
    return (youtubeVal == 'true');
}

function GetUploadPrefix(container) {
    var prefix = jQuery(container).find('.upload_prefix').val();
    if (prefix != null) {
        return prefix;
    }
    else {
        return '';
    }  
}

function GetUploadPostId(container)
{
    _postId = (jQuery(container).find('postId').val());
    if (_postId == null) {
        _postId = 0;
    }
}

function UploadIsMandatory(container)
{
    var mandatoryValue = jQuery(container).find('.upload_mandatory').val();
    return (mandatoryValue == 'true');
}

function AppendBtnHtml(container) {
    var btnTxt = 'adicionar mídia';
    if (UploadIsSingle(container)) {
        btnTxt = 'selecionar mídia';
    }
    var index = jQuery(container).find('.index').val();
    var html = '<div class="upload_btn_clear"></div><a class="upload_btn_item button" href="javascript:void(0);" id="fam_btn_upload_' + index + '" onclick="OpenMediaManager(' + _postId + ', ' + index + ',' + DisableYoutubeVideo(container) + ');" class="button">' + btnTxt + '</a>';
    var upName = jQuery(container).find('.upName').val();
    if (upName == null || upName == 'undefined') {
        upName = '';
    }
    else {
        var upName = " - " + upName;
    }
    html += '<span class="description">Clique para escolher' + upName + '</span>';
    jQuery(container).append(html);
}

function Is_pupload(container) {
    if ($(container).parents('.plupload-upload-uic').length > 0) {
        return true;
    }
    return false;
}

function CheckMediaAdded() {    
        var runAgain = true;
        jQuery(".media-item").each(function () {
            var mediaItem = this;
            var info = jQuery(this).find('.media-item-info');
            var info = jQuery(this).find('.media-item-info');
            if (window.location.href.toString().indexOf('selection=famupload') > -1) {
                var selectBtn = jQuery(this).find('.selectbtn');                
                if (info.length > 0 && selectBtn.length == 0) {
                    jQuery("#save-all").val('usar esta imagem');
                    jQuery("#save").val('usar esta imagem');
                    CreateLibraryBtn(mediaItem);
                    runAgain = false;
                }
            }
            else {
                if (window.location.href.toString().indexOf('selection=crop') > -1) {                    
                    var cropBtn = jQuery(this).find('.crop_btn');
                    if (info.length > 0 && cropBtn.length == 0) {
                        jQuery("#save-all").val('usar esta imagem');
                        jQuery("#save").val('usar esta imagem');
                        CreateCropSelectBtn(mediaItem);
                        runAgain = false;
                    }
                }
            }
            
        });
        if (runAgain) {
            clearTimeout(_checkMediaAdded_time_out);            
            _checkMediaAdded_time_out =window.setTimeout("CheckMediaAdded()", 1000);
        }

       
    
    //CreateCropSelectBtn(mediaItem);
    
}

jQuery(window).load(function ($) {

    try {
        if (uploader == 'undefined');
        uploader.bind('FileUploaded', function (up, files) {
            clearTimeout(_checkMediaAdded_time_out);
            _checkMediaAdded_time_out = window.setTimeout("CheckMediaAdded()", 500);            
        });
       
    } catch (err) { }

});

(function ($) {
    var origAppend = $.fn.append;
    $.fn.append = function () {
        return origAppend.apply(this, arguments).trigger("append");
    };
})(jQuery);

jQuery(document).ready(function () {
    SetUpFamPupload();
    if (window.location.href.toString().indexOf('selection=famupload') > -1) {
        if ($('#filter').find('input[name=selection]').length == 0) {
            $('#filter').append('<input type="hidden" name="selection" value="famupload">');
        }
        jQuery('#tab-type_url').remove();
        jQuery('head').append("<style> .savesend input {display:none !important;}</style>");
        jQuery("#media-items").bind("append", function () {
            jQuery("#save-all").val('usar esta mídia');
            jQuery("#save").val('usar esta mídia');
            
            clearTimeout(_checkMediaAdded_time_out);
            _checkMediaAdded_time_out = window.setTimeout("CheckMediaAdded()", 100);
        });
        if (window.location.href.toString().indexOf('disableyoutube=yes') > -1) {
            jQuery('#tab-youtube_video').remove();
        }
    }
    jQuery(".fam_delete_img").live('click', function () {
        var container = jQuery(this).parent().parent().parent();
        jQuery(this).parent().remove();        
        var btnTxt = 'adicionar mídia';
        if (UploadIsSingle(container)) {
            btnTxt = 'selecionar mídia';
        }
        var uploadBtn = jQuery(container).find('a');
        var otherImgs = jQuery(container).find("ul li");

        if (uploadBtn.length == 0 && (UploadIsSingle(container) || otherImgs.length == 0)) {
           
            if (Is_pupload(container)) {
                $(container).parents('.plupload-upload-uic').find('input[type=button]').show();
            }
            else {
                AppendBtnHtml(container);
            }
        }
        jQuery(container).find('li').each(function () {
            if (jQuery(this).has('*').length == 0) {
                jQuery(this).remove();
            }
        });
    });
    
    jQuery(".fam_is_cover").live('click', function () {
        var currentElem = this;
        jQuery(".fam_is_cover").prop( "checked", false );
        jQuery(currentElem).prop( "checked", true );  
        jQuery(".fam_is_cover").checkboxradio('refresh');   
       
    });

    if (window.location.href.toString().indexOf('selection=famupload') > -1) {
        jQuery('#media-items .media-item').each(function () {
            CreateLibraryBtn(this);
        });
    }


    jQuery("#save-all").live("click", function (e) {
        e.preventDefault();
        var info = jQuery("#media-items").find('.media-item-info');
        if (info.length > 0) {
            HandleSaveLibrarySaveBtn(this);
        }
    });

    jQuery("#save").live("click", function (e) {
        e.preventDefault();
        var info = jQuery("#media-items").find('.media-item-info');
        if (info.length > 0) {
            HandleSaveLibrarySaveBtn(this);
        }
    });    

    jQuery('.fam_upload').each(function (index) {        
        var uploadId = index + 1;
        jQuery(this).append('<input type="hidden" name="index" class="index" value="' + uploadId + '" />');
        
        if (Is_pupload(this)) {
            $(this).parents('.plupload-upload-uic').find('input[type=button]').show();
        }
        else {
            AppendBtnHtml(this);
        }

        if (!UploadIsSingle(this)) {

            var itens = $(this).find('ul li');
            if (itens.length == 1 || is_mobile) {
                $(this).find('ul li .mover').hide();
            }
            else {
                $(this).find('ul').sortable();
            }
        }
        else {
            $(this).find('ul li .mover').hide();
        }
       
        if (UploadIsSingle(this) && jQuery(this).find('ul li').length > 0) {

            if (Is_pupload(this)) {
                $(this).parents('.plupload-upload-uic').find('input[type=button]').hide();
            }
            else {
                jQuery(this).find('a.upload_btn_item').remove();
                jQuery(this).find('.description').remove();
            }

            
        }        
    });

    jQuery('#submit').click(function (e) {
        var uploadMandatoryFail = false;
        jQuery('.fam_upload').each(function () {            
            if (UploadIsMandatory(this) && jQuery(this).find('ul li').length == 0) {
                uploadMandatoryFail = true;                
            }         
        });
        if(uploadMandatoryFail)
        {
            e.preventDefault();
            alert('Você deve selecionar uma media para o(s) campos de upload obrigatórios');
        }
             
    });

    jQuery('#publish').click(function (e) {
        var uploadMandatoryFail = false;
        jQuery('.fam_upload').each(function () {
            if (UploadIsMandatory(this) && jQuery(this).find('ul li').length == 0) {
                uploadMandatoryFail = true;
            }
        });
        if (uploadMandatoryFail) {
            e.preventDefault();
            alert('Você deve selecionar uma media para o(s) campos de upload obrigatórios');
        }

    });
    
});

function plu_show_thumbs(imgId) {
    var $ = jQuery;
    var thumbsC = $("#" + imgId + "plupload-thumbs");
    thumbsC.html("");
    // get urls
    var imagesS = $("#" + imgId).val();
    var images = imagesS.split(",");
    for (var i = 0; i < images.length; i++) {
        if (images[i]) {
            var thumb = $('<div class="thumb" id="thumb' + imgId + i + '">\
                                <img src="' + images[i] + '" alt="" />\
                                <div class="thumbi"><a id="thumbremovelink' + imgId + i + '" href="#">Remove</a>\
                                </div>\
                                <div class="clear"></div>\
                            </div>');
            thumbsC.append(thumb);
            thumb.find("a").click(function () {
                var ki = $(this).attr("id").replace("thumbremovelink" + imgId, "");
                ki = parseInt(ki);
                var kimages = [];
                imagesS = $("#" + imgId).val();
                images = imagesS.split(",");
                for (var j = 0; j < images.length; j++) {
                    if (j != ki) {
                        kimages[kimages.length] = images[j];
                    }
                }
                $("#" + imgId).val(kimages.join());
                plu_show_thumbs(imgId);
                return false;
            });
        }
    }
    if (images.length > 1) {
        thumbsC.sortable({
            update: function (event, ui) {
                var kimages = [];
                thumbsC.find("img").each(function () {
                    kimages[kimages.length] = $(this).attr("src");
                    $("#" + imgId).val(kimages.join());
                    plu_show_thumbs(imgId);
                });
            }
        });
        thumbsC.disableSelection();
    }
}

function SetUpFamPupload() {
    if ($(".plupload-upload-uic").length > 0) {
        var pconfig = false;
        $(".plupload-upload-uic").each(function () {
            var $this = $(this);
            var id1 = $this.attr("id");
            var imgId = id1.replace("plupload-upload-ui", "");

            plu_show_thumbs(imgId);

            pconfig = JSON.parse(JSON.stringify(base_plupload_config));

            pconfig["browse_button"] = imgId + pconfig["browse_button"];
            pconfig["container"] = imgId + pconfig["container"];
            pconfig["drop_element"] = imgId + pconfig["drop_element"];
            pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
            pconfig["multipart_params"]["imgid"] = imgId;
            pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
            pconfig["runtimes"] = "html5,silverlight,flash,html4";
            pconfig["max_file_size"] = '1000097152b'; 
           

            if ($this.hasClass("plupload-upload-uic-multiple")) {
                pconfig["multi_selection"] = true;
            }

            if ($this.find(".plupload-resize").length > 0) {
                var w = parseInt($this.find(".plupload-width").attr("id").replace("plupload-width", ""));
                var h = parseInt($this.find(".plupload-height").attr("id").replace("plupload-height", ""));
                pconfig["resize"] = {
                    width: w,
                    height: h,
                    quality: 90
                };
            }

            var uploader = new plupload.Uploader(pconfig);

            uploader.bind('Init', function (up) {

            });

            uploader.init();

            // a file was added in the queue
            uploader.bind('FilesAdded', function (up, files) {
                $.each(files, function (i, file) {
                    $this.find('.filelist').append(
                        '<div class="file" id="' + file.id + '"><b>' +

                        file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                        '<div class="fileprogress"></div></div>');
                });

                up.refresh();
                up.start();
            });

            uploader.bind('UploadProgress', function (up, file) {

                $('#' + file.id + " .fileprogress").width(file.percent + "%");
                $('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
            });

            uploader.bind('Error', function (uploaderObj, data, file) {
                if (data.message == "File size error.") {
                    alert("Cada arquivo deve ter no máximo 10 MB");
                }
                
            });

            // a file was uploaded
            uploader.bind('FileUploaded', function (up, file, response) {
                var response_file = $.parseJSON(response["response"]);
                $('#' + file.id).fadeOut();
                //response = response["response"]
                // add url to the hidden field
                if ($this.hasClass("plupload-upload-uic-multiple")) {
                    // multiple
                    var v1 = $.trim($("#" + imgId).val());
                    if (v1) {
                        v1 = v1 + "," + response_file.url;
                    }
                    else {
                        v1 = response_file.url;
                    }
                    $("#" + imgId).val(v1);
                }
                else {
                    // single
                    $("#" + imgId).val(response_file.url + "");
                }

                // show thumbs 
                //setSelectedImage(response_file.id, response_file.url, response_file.name, '0');
                var uploaderId = $('#' + imgId).next().find('.fam_upload .index').val();
                if (typeof (uploaderId) == "undefined") {
                    uploaderId = $('.fam_upload .index').first().val();
                }
                setSelectedImage(response_file.id, response_file.url, response_file.name, uploaderId);
                //plu_show_thumbs(imgId);
            });



        });
    }
}




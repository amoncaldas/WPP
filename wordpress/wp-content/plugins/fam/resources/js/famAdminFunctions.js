_checkinProceeded = false;
_postProceeded = false;
_cachedPlacesResult = [];
var _postData = false;

$(document).ready(function () {
    $('.fancybox').fancybox();
    FilterCheckinPlace();

    $("#facebook_checkin").change(function () {
        if (!$("#facebook_checkin").is(':checked')) {
            $('.places_suggestion').html('');
        }
    });

    $('#teste_send_informativo').click(function (e) {
        var destiny_email = prompt("Informe o email que receberá o informativo de teste", "");

        if (isValidEmailAddress(destiny_email)) {
            TestarEnvioDeInformativo(destiny_email);
        }
        else if(destiny_email != null){
            alert('O email de destino informado não parece ser válido');
        }
    });
    
    jQuery('#publish,#submit').click(function (e) {
        var btnProceeding = $(this).hasClass('fam_publishing');
        $('#publishing-action .spinner').show();
        var btnid = '#' + $(this).attr("id");
        if (!btnProceeding) {
            $(this).addClass('button-primary-disabled');
            $(this).addClass('fam_publishing');
            if (!ValidateForm()) {
                e.preventDefault();
                window.setTimeout(function () {
                    $('#publishing-action .spinner').hide();
                    $(btnid).removeClass('button-primary-disabled');
                    $(btnid).removeClass('fam_publishing');
                    
                }, 100);
            
            }
            else {
                if (_checkinProceeded == false && _postProceeded == false) {
                    e.preventDefault();
                    if (($('#facebook_checkin').length > 0 && $('#facebook_checkin').attr('checked')) || ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked')) || ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked'))) {
                        var link = UpdatePost('publish', '', SetPermalink);
                        //CheckPopUpBlockerAndShare(btnid, SahareContent);
                        SahareContent(btnid);                       
                    }
                    else {
                        SubmitAction(this);
                    }
                }
                else {                  
                    SubmitAction(this);                       
                }
            }
        }
    });

    $('#title').blur(function () {
        UpdatePost('keep-status', '', SetPermalink);
    });

    $('#conteudo, #descricao_album').focus(function () {
        if ($(this).val().indexOf('Digite o texto do status aqui') > -1) {
            $(this).val($(this).val().replace('Digite o texto do status aqui', ''));
        }
        if ($(this).val().indexOf('Digite a descrição do álbum aqui') > -1) {
            $(this).val($(this).val().replace('Digite a descrição do álbum aqui', ''));
        }

    });
    $('#conteudo, #descricao_album').blur(function () {
        if ($(this).val().length == 0) {
            if ($(this).attr('name') == 'conteudo') {
                $(this).val('Digite o texto do status aqui');
            }
            if ($(this).attr('name') == 'descricao_album') {
                $(this).val('Digite a descrição do álbum aqui');
            }
        }
    });    
});

function SetPermalink(permalink)
{
    if ($('#sample-permalink').length > 0) {
        $('#sample-permalink').html(permalink);
    }
    else {
        $('#edit-slug-box').html('<strong>Link Permanente</strong>' + '<span id="sample-permalink"tabindex="-1" >' + permalink + '</span>');
    }
}

function CancelSave(btnid) {    
    $('#publishing-action .spinner').hide();
    $(btnid).removeClass('button-primary-disabled');
    $(btnid).removeClass('fam_publishing');
}

jQuery(window).load(function ($) {
    //SetUploadResizeOnCliente();
});

function SetUploadResizeOnCliente() {
    try {
        if (typeof (uploader) != 'undefined') {
            uploader.settings.max_file_size = '200097152b';
            uploader.settings['resize'] = { width: 1600, height: 1024, quality: 75 };
        }

    } catch (err) { }
}

function ValidateForm() {
    var valid = true;
    if ($('#editable-post-name-full').length > 0) {
        $link = $('#editable-post-name-full').clone().children().remove().end().text();
    }
    else {
        $link = $('#sample-permalink').clone().children().remove().end().text();
    }
    if ($('#title').length == 1 && $('#title').val() == null || $('#title').val() == '') {        
        alert('Você deve informar um título');
        $('#title').focus();
        valid = false;
    }

    
    if (valid && $link.indexOf('/albuns/') == -1 && $link.indexOf('/blog/') == -1 && ($('#local').length == 1 && $('#local').val() == null || $('#local').val() == '')) {
        alert('Você deve informar um local');
        $('#local').focus();
        valid = false;
    }
    var textAreaValue = $("#content_ifr").contents().find("#tinymce").html();    
    if (valid && ($('textarea#content').length == 1 && (textAreaValue == '' && $('textarea#content').val() == ''))) {
        alert('Você deve informar um conteúdo');
        $('#content').focus();
        valid = false;
    }     
    
    if (valid && ($('#conteudo').length == 1 && GetPostContent() == '')) {
        if ($('#conteudo').attr('name') == 'descricao_album') {
            alert('Você deve digitar uma descrição para o álbum');
        }
        else {
            alert('Você deve digitar um conteúdo');
        }
        $('#conteudo').focus();
        valid = false;
    }

   
    if ($link.indexOf('/forum/') > -1 || $link.indexOf('/blog/') > -1) {
        valid = false;
        $categories = $("#taxonomy-category .selectit input[name='post_category[]'], #taxonomy-category .popular-category  input[name='post_category[]");
        $($categories).each(function () {
            valid = $(this).is(":checked");
            if (valid) {
                return false;
            }
        });
        if (!valid) {
            alert('Você deve selecionar ao menos uma categoria');
        }
    }
    if ($('#tocrop').length > 0) {
        if (!cropped) {
            if ((imgHeight != cropWidth || cropWidth != imgWidth) && (imgHeight + 1 != cropHeight || imgWidth + 1 != cropWidth)) {                
                alert('Você deve selecionar uma imagem e recortá-la antes de publicar');
                valid = false;
            }
        }
        else {
            window.onbeforeunload = null;
            window.onunload = null;
        }
    }
      
    return valid;
}

function SahareContent(btnid) {   

    if ($('#facebook_checkin').length > 0 && $('#facebook_checkin').attr('checked')) {
        
        LoadFacebookAPi();
        try
        {
            if ($('#latitude').length > 0 && $('#latitude').val() != '' && $('#longitude').length > 0 && $('#longitude').val() != '') {

                var selectedPlaceId = $('#place_id').html();
                if (typeof (selectedPlaceId) != 'undefined' && selectedPlaceId != null && selectedPlaceId != '') {
                    var place_id = selectedPlaceId;
                    DoCheckin(place_id, btnid);
                }
                else {
                    try
                    {
                        var $lat = $('#latitude').val();
                        var $long = $('#longitude').val();
                        FB.login(function (response) {
                            if (response.authResponse) {
                                FB.api(
                                {
                                    method: 'fql.query',
                                    query: "SELECT name,page_id,name,description,type FROM place WHERE is_city and distance(latitude,longitude, '" + $lat + "', '" + $long + "') < 100000 ORDER BY distance(latitude,longitude, '" + $lat + "', '" + $long + "') LIMIT 2",
                                },
                                function (data) {
                                    if (!data || data.error_code) {
                                        console.log('error to get place');
                                        if ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked')) {
                                            _checkinProceeded = true;
                                            DoPostWithLogin(btnid);
                                        }
                                        else {
                                            alert('Não foi possível fazer checkin mas o post será publicado');
                                            SubmitAction(btnid);
                                        }
                                    }

                                    else {
                                        typ = typeof (data);
                                        if (data instanceof Array && data.length > 0) {
                                            var firstPlace = data[0];
                                            place_id = firstPlace.page_id;
                                        }
                                        if (typeof (place_id) != 'undefined' && place_id > 0) {
                                            DoCheckin(place_id, btnid);
                                        }
                                        else {
                                            if ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked')) {
                                                _checkinProceeded = true;
                                                DoPostWithLogin(btnid);
                                            }
                                            else {
                                                SubmitAction(btnid);
                                            }
                                        }
                                    }
                                }
                                );
                            }
                            else {
                                alert('Não foi possível compartilhar  o conteúdo no facebook mais ele será publicado no site');
                                SubmitAction(btnid);
                            }
                        }, GetScope());
                    }
                    catch (ex) {
                        if ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked')) {
                            _checkinProceeded = true;
                            DoPostWithLogin(btnid);
                        }
                        else {
                            SubmitAction(btnid);
                        }
                    }
                       
                }
                
            }
            else {
                if ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked')) {
                    _checkinProceeded = true;                                    
                    DoPostWithLogin(btnid);
                }
                else {
                    SubmitAction(btnid);
                }
            }
        }
        catch (ex) {
            _checkinProceeded = true;
            _postProceeded = true;
            //$(btnid).trigger('click');
            SubmitAction(btnid);
        }

    }
    else 
    {
        
        if ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked'))
        {           
            _checkinProceeded = true;
            try
            {
                FB.login(function (response) {
                    DoPost(btnid);
                }, GetScope());
            }
            catch (ex) {
                _checkinProceeded = true;
                _postProceeded = true;
                //$(btnid).trigger('click');
                SubmitAction(btnid);
            }
            
        }
        else {
            _checkinProceeded = true;           
            if ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked')) {
                try {
                    FB.login(function (response) {
                        DoFAMPost(btnid);
                    }, GetScope());
                }
                catch (ex) {
                    _checkinProceeded = true;
                    _postProceeded = true;
                    //$(btnid).trigger('click');
                    SubmitAction(btnid);
                }
            }
            else {
                _postProceeded = true;
                //$(btnid).trigger('click');
                SubmitAction(btnid);
            }
        } 
    }
}

function DoCheckin(place_id, btnid) {
   
    postData = GetPostData();
    FB.login(function (response) {
        if (response.authResponse) {
            var checkinText = postData.conteudo;
            if (postData.type == 'relato') {
                checkinText = postData.name;
            }
            
            FB.api('/me/feed', 'post', { message: checkinText + ' | Em ' + postData.local, place: place_id },
            function (response) {
                if (!response || response.error) {
                        console.log('checkin error: ' + response.error.message);
                        alert('Não foi possível o checkin no facebook, mas o post será publicado');
                        _checkinProceeded = true;
                        _postProceeded = true;
                        //$(btnid).trigger('click');
                        SubmitAction(btnid);

                    } else {
                        DoFAMPost();
                        _checkinProceeded = true;
                        if ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked')) {
                            DoPost(btnid);
                        }
                        else{
                            if ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked')) {
                                DoFAMPost(btnid);
                            }
                            else {
                                //$(btnid).trigger('click');
                                SubmitAction(btnid);

                            }
                        }
                        
                        console.log('Checkin ID: ' + response.id);
                    }
                }
            );
        }
        else {
            alert('Não foi possível fazer login no facebook, mas o post será publicado');
            _checkinProceeded = true;
            if ($('#facebook_wall').length > 0 && $('#facebook_wall').attr('checked')) {
                DoPost(btnid);
            }
            else {
                //$(btnid).trigger('click');
                SubmitAction(btnid);
            }
        }

    }, GetScope());
}

function DoPost(btnid) {
    if (!_postProceeded) {
        postData = GetPostData();
        //message, picture, link, name, caption, description, source, place, tags  
        var local = '';
        if (typeof (postData.local) != 'undefined' && postData.local != 'undefined' && postData.local != '') {
            local = ' | Em ' + postData.local;
        }
        
        FB.api('/me/feed', 'post', {
            message: postData.name + local,
            picture: postData.picture,
            link: postData.link, 
            name: postData.name,
            caption: postData.link,
            description: postData.conteudo            
            },
            function (response) {
                if (!response || response.error) {
                    _postProceeded = true;
                    console.log('Post error: ' + response.error.message);
                    if ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked')) {
                        DoFAMPost(btnid);
                    }
                    else {
                        //$(btnid).trigger('click');
                        SubmitAction(btnid);

                    }

                }
                else {
                    _postProceeded = true;
                    if ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked')) {
                        DoFAMPost(btnid);
                    }
                    else {
                        //$(btnid).trigger('click');
                        SubmitAction(btnid);

                    }
                    console.log('Post ID: ' + response.id);
                }
            }
       );
    }
}

function DoPostWithLogin(btnid) {
    if (!_postProceeded) {
        FB.login(function (response)
        {
            if (response.authResponse) {
                postData = GetPostData();
                //message, picture, link, name, caption, description, source, place, tags  
                var local = '';
                if (typeof (postData.local) != 'undefined' && postData.local != 'undefined' && postData.local != '') {
                    local = ' | Em ' + postData.local;
                }

                FB.api('/me/feed', 'post', {
                    message: postData.name + local,
                    picture: postData.picture,
                    link: postData.link,
                    name: postData.name,
                    caption: postData.link,
                    description: postData.conteudo
                },
                    function (response) {
                        if (!response || response.error) {
                            _postProceeded = true;
                            console.log('Post error: ' + response.error.message);
                            if ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked')) {
                                DoFAMPost(btnid);
                            }
                            else {
                                //$(btnid).trigger('click');
                                SubmitAction(btnid);

                            }

                        }
                        else {
                            _postProceeded = true;
                            if ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked')) {
                                DoFAMPost(btnid);
                            }
                            else {
                                //$(btnid).trigger('click');
                                SubmitAction(btnid);

                            }
                            console.log('Post ID: ' + response.id);
                        }
                    }
               );
            }
            else {
                alert('Não foi possível compartilhar  o conteúdo no facebook mais ele será publicado no site');
                SubmitAction(btnid);
            }
       }, GetScope());

    }

}


function GetPlaceId() {
    return $('#place_id').html();
       
}

function DoFAMPost(btnid) {
    if ($('#fam_page_status').length == 1 && $('#fam_page_status').attr('checked')) {
        postData = GetPostData();
        var local = '';
        if (typeof (postData.local) != 'undefined' && postData.local != 'undefined' && postData.local != '') {
            local = ' | Em ' + postData.local;
        }
        var fam_page_id = '222705747761628';
        if (location.href.toString().indexOf('teste.') > -1) {
            fam_page_id = '604453679565803';
        }
        
        FB.api('/me/accounts', function (response) {            
            var responseIsArray = response.data instanceof Array;
            var reponseLenght = $(response.data).length;
            if (responseIsArray && reponseLenght > 0) {
                var foundFam = false;
                var page;
                $(response.data).each(function (index, value) {
                    if (value.id == '222705747761628') {
                        page = value;
                        foundFam = true;
                    }
                });

                if (foundFam) {
                    FB.api('/' + fam_page_id + '/feed', 'post', {
                        message: postData.name + local,
                        access_token: page.access_token,
                        picture: postData.picture,
                        link: postData.link,
                        name: postData.name,
                        caption: postData.link,
                        description: postData.conteudo
                    },
                        function (response) {
                            if (!response || response.error) {
                                console.log('FAM Post error: ' + response.error.message);
                                //$(btnid).trigger('click');
                                SubmitAction(btnid);
                            }
                            else {
                                console.log('FAM Post ID: ' + response.id);
                                //$(btnid).trigger('click');
                                SubmitAction(btnid);
                            }
                        }
                    );
                }
                else {
                    FB.api('/' + fam_page_id + '/feed', 'post', {
                        message: postData.name + local,
                        picture: postData.picture,
                        link: postData.link,
                        name: postData.name,
                        caption: postData.link,
                        description: postData.conteudo
                    },
                        function (response) {
                            if (!response || response.error) {
                                console.log('FAM Post error: ' + response.error.message);
                                //$(btnid).trigger('click');
                                SubmitAction(btnid);
                            }
                            else {
                                console.log('FAM Post ID: ' + response.id);
                                //$(btnid).trigger('click');
                                SubmitAction(btnid);
                            }
                        }
                    );
                }
            }
            else {
                FB.api('/' + fam_page_id + '/feed', 'post', {
                    message: postData.name + local,
                    picture: postData.picture,
                    link: postData.link,
                    name: postData.name,
                    caption: postData.link,
                    description: postData.conteudo
                },
                    function (response) {
                        if (!response || response.error) {
                            console.log('FAM Post error: ' + response.error.message);
                            //$(btnid).trigger('click');
                            SubmitAction(btnid);
                        }
                        else {
                            console.log('FAM Post ID: ' + response.id);
                            //$(btnid).trigger('click');
                            SubmitAction(btnid);
                        }
                    }
                );
            }
        })
       
    }
}

function GetPostContent() {
    var content = '';
    content = $('#conteudo').val();
    if (typeof (content) == 'undefined' || content == null || content == '') {
        content = $('#content').val();
    }
    if (typeof (content) == 'undefined' || content == null || content == '') {
        content = $('#descricao_album').val();
    }
    if (typeof (content) == 'undefined' || content == null || content == '') {
        content = '';
    }
    
    if (content.indexOf('Digite o texto do status aqui') > -1) {
        content = content.replace('Digite o texto do status aqui','');
    }
    if (content.indexOf('Digite a descrição do álbum aqui') > -1) {
        content = content.replace('Digite a descrição do álbum aqui','');
    }

   
    return content;
}

function GetPostData()
{
    if (_postData != false) {
        return _postData;
    }
    else {
        var postData = new Object();
        if (location.href.toString().indexOf('/m-admin') > -1) {
            postData.site = location.href.toString().split('/m-admin')[0];
        }
        else {
            postData.site = location.href.toString().split('/wp-admin')[0];
        }
        postData.conteudo = GetPostContent();

        postData.conteudo = postData.conteudo.replace(/\[caption.*?\]/, '<div class="caption_wrapper">');
        postData.conteudo = postData.conteudo.replace(/\[\/caption\]/, '</div>');
        $('body').append('<div style="display:none;" id="post_content">' + postData.conteudo + '</div>');
        $('.caption_wrapper').replaceWith('<div></div>');
        postData.conteudo = $('#post_content').text();
        $('#post_content').remove();
        if (postData.conteudo.length > 2000) {
            postData.conteudo = postData.conteudo.substring(0, 2000) + '...';
        }

        postData.conteudo += ' - veja mais no site ' + postData.site.replace('http://', '');

        if ($('#local').length > 0) {
            postData.local = $('#local').val();
        }
        else {
            postData.local = '';
        }

        postData.dataVisita = $('#data_de_visita').val();
        if (typeof (postData.dataVisita) == 'undefined' || postData.dataVisita == null) {
            postData.dataVisita = '';
        }

        if ($('#sample-permalink').length > 0) {
            if ($('#editable-post-name-full').length > 10) {
                $link = $('#editable-post-name-full').clone().children().remove().end().text();
            }
            else {
                $link = $('#sample-permalink').clone().children().remove().end().text();
            }
            $link = $link.replace('status//', 'status/');
            $link = $link.replace('relatos//', 'relatos/');
            $link = $link.replace('relatos//', 'albuns/');
            $link = $link.replace('blog//', 'blog/');
            $link = $link.replace('forum//', 'forum/');
        }
        else {
            $link = location.href.toString().split('/wp-admin')[0];
        }
        postData.link = $link;
        if (postData.link.indexOf("/status") != -1) {
            postData.name = $('#title').val() + ' | Status de viagem';
            postData.type = 'status';
        }
        else if (postData.link.indexOf("/albuns") != -1) {
            postData.name = $('#title').val() + ' | Album de viagem';
            postData.type = 'album';
        }
        else if (postData.link.indexOf("/relatos") != -1) {
            postData.name = $('#title').val() + ' - Relato de viagem';
            postData.type = 'relato';
        }
        else if (postData.link.indexOf("/forum") != -1) {
            postData.name = $('#title').val() + ' | Tópico de fórum';
            postData.type = 'forum';
        }
        else if (postData.link.indexOf("/blog") != -1) {
            postData.name = $('#title').val() + ' | Blog Fazendo as Malas';
            postData.type = 'blog';
        }
        else {
            postData.name = $('#title').val() + ' | Destaque';
            postData.type = 'destaque';
        }

        var $picture = '';
        if (('.fam_upload img').length > 0) {
            var src = $('.fam_upload img').first().prop('src');
            if (typeof (src) != 'undefined' && src != null) {
                $picture = src;
            }
        }

        if ($picture == '' || $picture == null) {
            var src = $('#content_ifr').contents().find('body').find("img").first().prop('src');
            if (typeof (src) != 'undefined' && src != null) {
                src = src.replace(/[-]+\d+[x]+\d+[\.]/, '.');
                $picture = src;
            }
        }

        if ($picture == '') {
            if ($('#tocrop').length > 0 && $('#tocrop').prop('src') != null && typeof $('#tocrop').prop('src') != 'undefined') {
                $picture = $('#tocrop').prop('src');
                $picture = $picture.replace('/wp-admin', '');
                if ($picture.indexOf('?randon=') > -1) {
                    $picture = $picture.split('?randon=')[0];
                }
            }
        }

        if ($picture == null || $picture == false || $picture == '') {
            $picture = GetSiteMainImage(location.href);
        }

        if ($picture == null || $picture == false || $picture == '') {
            $picture = 'http://' + location.host + '/fazendo_as_malas.jpg';
        }
        postData.picture = $picture.replace('-120x70', '');
        postData.picture = postData.picture.replace('-119x69', '');
        if (postData.picture.indexOf('youtube.com') > 0) {
            postData.picture = postData.picture.replace('1.jpg', 'hqdefault.jpg');
        }
        _postData = postData;
        return postData;
    }
}

function LoadFacebookAPi()
{
    if (typeof (FB) == 'undefined' || FB == null) {
        window.fbAsyncInit = function () {
            FB.init({
                appId: '585138868164342', // App ID
                channelUrl: 'http:\/\/fazendoasmalas.com\/channel.php', //Channel File
                status: true, // check login status
                cookie: true, // enable cookies to allow the server to access the session
                xfbml: true  // parse XFBML
            });

        };
    }
}


function LoadPlaces(lat, long, term) {        
    
    var q = (term != null && term != 'undefined') ? 'q=' + term + '&' : '';
    var term = (term != null && term != 'undefined') ? term : '';
    var actionurl = 'https://graph.facebook.com/v2.7/search?' + q + 'type=place&center=' + lat + ',' + long + '&limit=10&distance=10000&access_token=';
    if(term == null || term == 'undefined')
    {
        $('#place_name').val('');
    }

    $('#place_name').css('background', "#fff url('/wp-admin/images/wpspin_light.gif') no-repeat right 2px");
    var chached = GetCachedPlaceSearch(lat, long, term);
    FB.getLoginStatus(function (response) {        
        if (response.authResponse) {            
            actionurl = actionurl + response.authResponse.accessToken;            
            if (chached) {
                ShowPlacesSuggestion(chached);
            }
            else {
                FB.api(actionurl, function (response) {
                    if (response.data) {
                        CachePlaceSearch(term, lat, long, response.data);
                        ShowPlacesSuggestion(response.data);
                    } else {
                        console.log("Error suggesting places!");
                    }
                });
            }
        } else {
            FB.login(function (response) {
                if (response.authResponse) {
                    actionurl = actionurl + response.authResponse.accessToken;
                    if (chached) {
                        ShowPlacesSuggestion(chached);
                    }
                    else {
                        FB.api(actionurl, function (response) {
                            if (response.data) {
                                CachePlaceSearch(term, lat, long, response.data);
                                ShowPlacesSuggestion(response.data);
                            } else {
                                console.log("Error suggesting places!");
                            }
                        });
                    }
                }
            });
        }
    });
    
}

function CachePlaceSearch(term, lat, long, itens) {

    var found = false;
    $(_cachedPlacesResult).each(function () {
        if (this[0] == term && this[1] == lat && this[2] == long) {
            found = true;
        }
    });
    if (!found) {
        var new_cache = [term, lat, long, itens];
        _cachedPlacesResult.push(new_cache);
    }
   
}

function GetCachedPlaceSearch(lat, long, term) {   
    var foundchache = false;
    $(_cachedPlacesResult).each(function () {
        if (this[0] == term && this[1] == lat && this[2] == long) {
            foundchache = this[3];
        }
    });
    return foundchache;
  
}

function FilterCheckinPlace() {
    inputSelector = '#place_name';    
    $(inputSelector).keydown(function (e) {        
        $("#facebook_checkin").prop("checked", true);
        var $lat = $('#latitude').val();
        var $long = $('#longitude').val();
        var local = $('#local').val();
        if (typeof ($lat) == 'undefined' || typeof ($long) == 'undefined' || $long == null || $lat == null || $lat == '' || $long == '' && e.which != 46) {
            alert('Você deve primeiro escolher um local no campo local');
            $(inputSelector).val('');
        }
        else {
            var $place_name = $(inputSelector).val();
            if (typeof ($place_name) != 'undefined' && $place_name != null && $place_name.length > 1) {
                              
                var chached = GetCachedPlaceSearch($lat, $long, $place_name);
                if (chached) {                    
                    ShowPlacesSuggestion(chached);
                }
                else {
                    LoadPlaces($lat, $long, $place_name);
                }                
            }
            else {
                //$('.places_suggestion').remove();
            }
        }
    });

    $('.result_item').live('click', function () {
        $('#place_id').html($(this).find('.place_id').html());       
        var name = $(this).clone().children().remove().end().text();
        $(inputSelector).val(name);
        $('.places_suggestion').remove();
    });
}

function ShowPlacesSuggestion(results) {   
    $('#place_name').css('background', "#fff");
    var typ = typeof (results);
    var htmlSuggestion = '<ul class="places_suggestion">';
    var suggestionItens = '';
    if ($(results).length > 0) {
       
        $(results).each(function () {
            suggestionItens += '<li class="result_item"><span class="place_id" style="display:none;">' + this.id + '</span>' + this.name + '</li>';
        });
               
    }
    if (suggestionItens == '') {
        suggestionItens = '<li class="no_result">nenhum resultado</li>';
    }
    $('.places_suggestion').remove();    
    htmlSuggestion += suggestionItens;
    htmlSuggestion += "</ul>";
    $(inputSelector).after(htmlSuggestion);
    
}

function GetScope(permissions_array) {
    var permissions;
    if ($('#fam_page_status').length > 0 && $('#fam_page_status').attr('checked')) {
        permissions = { scope: 'publish_stream,manage_pages,status_update' }
        //permissions = { scope: 'publish_stream' }
    }
    else {
        permissions = { scope: 'publish_stream' }
    }
    return permissions;
}

function ReorderDestaques(site_id) {   
    var reorderposts = jQuery('.reorderposts ul li');
    if (jQuery(reorderposts).size() > 1) {
        jQuery('.reorderposts ul').sortable({
            opacity: 0.6,
            cursor: 'move',
            update: function () {                                       
                var success = false;
                var reorderposts = jQuery('.reorderposts ul li');
                $('#status_process').html('Atualizando. Aguarde...');
                var url = '/FAMCore/Async/action.php?action=reorder_posts';
                var itens = '';
                jQuery(reorderposts).each(function (index, value) {
                    if (index > 0) {
                        itens += ";";
                    }
                    itens += $(value).find('.itenId').val() + ',' + (index + 1).toString();

                })
                var params = {
                    save_posts_order: 'yes',
                    posts_itens_order: itens,
                    'site_id': site_id
                }
							
                jQuery.ajax({
                    type: 'post',
                    url: url,
                    cache: false,
                    data: params,
                    dataType: 'html',
                    success: function (data, status) {
                        if (data != null && data == 'success') {
                            success = true;
                            $('#status_process').html('Atualizado!');
                            window.setTimeout(function () {
                                $('#status_process').html('');
                            },2000)
                        }
                        else {
                            success = false;                                        
                            alert('Houve uma falha ao salvar o reordenamento');
                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        success = false;
                    }
                });
                            
            }
        });
    }
    

}

function SubmitAction(btn) {   
    btn = $(btn);
    if (is_mobile) {
        var btn_id = $(btn).attr('id');
        var action_url = location.href.toString();
        if (btn_id == 'publish') {
            action_url = location.href.toString().replace('action=new', 'action=save');
            action_url = action_url.replace('action=edit', 'action=update');
        }
        else if (btn_id == 'draft') {
            action_url = location.href.toString().replace('action=new', 'action=draft');
            action_url = action_url.replace('action=edit', 'action=draft');
        }
        document.getElementById('content_form').action = action_url;
        document.getElementById('content_form').submit();
    }
    else {
        $(btn).trigger('click');
    }
    
}

function UpdatePost(status, upload_prefix, callback) {
   upload_prefix = typeof supress_error !== 'undefined' ? supress_error : '';
   var has_callback_function = typeof callback !== 'undefined' ? true : false;
    var returnValue = '';
    var mediasId = '';
    $('.fam_upload input[type=hidden]').each(function (index, value) {
        if ($(this).attr('id')) {
            if ($(this).attr('id').indexOf('_fam_upload_id_') > -1) {
                if (mediasId == '') {
                    mediasId += $(this).val();
                }
                else {
                    mediasId += (';' + $(this).val());
                }
            }
        }
        
    });
    $.ajax({
        url: '/FAMCore/Async/action.php',
        type: 'post',
        dataType: 'html',
        async: has_callback_function,
        data: {
            'action': 'update_post',
            'title': $('#title').val(),
            'id': $('#post_ID').val(),
            'content': GetPostContent(),
            'status':status,
            'local': $('#local').val(),
            'latitude': $('#latitude').val(),
            'longitude': $('#longitude').val(),
            'mediasId': mediasId,
            'site_url': location.href,
            'upload_prefix': upload_prefix,
            'seo_desc': $('#seo_desc').val()
        },
        success: function (data) {
            if (data == "error") {
                returnValue = '';
            }
            else {
                returnValue = data;
                if (has_callback_function) {
                    callback(returnValue);
                }
            }
        },
        error: function (request, error) {
            returnValue = false;
        }
    });
    if (!has_callback_function) {
        return returnValue;
    }
    
}

function TestarEnvioDeInformativo(email_destino) {
    var content = GetPostContent();
    var url_destiny = $('#url_informativo').val();
    var title = $('#title').val();
    $.ajax({
        url: '/FAMCore/Async/action.php',
        type: 'post',
        dataType: 'html',
        async: true,
        data: {
            'action': 'teste_envio_informativo',
            'title': title,
            'content': content,
            'destiny_email': email_destino,
            'url_destiny': url_destiny
        },
        success: function (data) {
            if (data == "error") {
                alert("Ocorreu um erro ao enviar  o email de teste");
            }
            else {
                alert("Email de teste enviado com sucesso para " + email_destino);
            }
        },
        error: function (request, error) {
            alert("Ocorreu o seguinte erro ao enviar  o email de teste: " + JSON.stringify(error));
        }
    });
}

function CheckPopUpBlockerAndShare(btnid, callback) {
    var myPopup = window.open("/termos-de-uso-do-forum/", "", "directories=no,height=10,width=10,menubar=no,resizable=no,scrollbars=no,status=no,titlebar=no,top=0,location=no");
    if (!myPopup) {
        CancelSave(btnid);
        alert('O bloqueador de pop-ups está ativo e por isso não é possível abrir a janela de autenticação do Facebook. Você deve desabilitar o bloqueador de pop-up ou desmarcar as publicações no Facebook. Seu post não foi salvo nem publicado no Facebook.');
    }
    else {
        myPopup.onload = function () {
            setTimeout(function () {
                if (myPopup.screenX === 0) {
                    try{
                        myPopup.close();
                    }
                    catch (ex) { }
                    CancelSave(btnid);
                    alert('O bloqueador de pop-ups está ativo e por isso não é possível abrir a janela de autenticação do Facebook. Você deve desabilitar o bloqueador de pop-up ou desmarcar as publicações no Facebook. Seu post não foi salvo nem publicado no Facebook.');
                } else {
                    myPopup.close();
                    callback(btnid);
                }
            }, 0);
        };
    }
}

function GetSiteMainImage(site_url) {
    var returnValue = false;
    $.ajax({
        url: '/FAMCore/Async/action.php',
        type: 'post',
        dataType: 'html',
        async: false,
        data: {
            'action': 'get_site_main_image',
            'site_url': site_url,
        },
        success: function (data) {
            if (data == "error") {
                returnValue = false;
            }
            else {
                returnValue = data;
            }
        },
        error: function (request, error) {
            returnValue = false;
        }
    });
    return returnValue;   
}



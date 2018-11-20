var basepath = "/wp-content/themes/FAMMobile/";

var loadingContent = false;
var originalOgImagem;
var openFancyboxUrl;
var animationTimeOut;
var commentText;

document.oncopy = AddLinkOnCopy;

LoadPlusOne();

jQuery(document).ready(function () {    
    ConfigMediaShow();    
    HandleOpenMedia();
    HandleCommentForm();  
    SetSendEmailListener();    
    SetFancyBoxDefault();
    RenderYoutubeVideos();
    notify_to_fb_comment();
    SetUpNewsOptin();
    LoadPromptSocialTicker(50000);

    if (location.pathname.indexOf('/m-admin/')) {
        setCookie('avoid_analytics', 'yes', 1000);
    }

    if (location.pathname == '/albuns/' || location.pathname.indexOf('/albuns') > -1) {
        LoadMoreOnScrool("#bloco-conteudo-central .medias-album .moreContent", 300, 100, 4);
    }
    if (location.pathname == '/atualizacoes/' || location.pathname.indexOf('/atualizacoes') > -1) {
        LoadMoreOnScrool(".atualizacaoInterna .moreContent", 300, 20, 2);
    }
    LoadMoreOnScrool(".relatos .moreContent", 300, 20, 2, true);
    LoadMoreOnScrool(".fotosevideos .moreContent", 300, 100, 4, true);
    LoadMoreOnScrool("#coluna-lateral-direita .galeriafoto .moreContent", 300, 50, 2, true);

    if (location.search.indexOf('?s=') > -1 && location.search.length > 2) {
        LoadMoreOnScrool(".search-itens .moreContent", 300, 50, 4);
    }
    $(window).resize(function () {
        AdjustforumShow();
    });

    $('.main_menu_anchor').click(function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $('.menu_home').offset().top + -35 }, 2000);
    })
    $('.go_to_top').click(function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 2000);
    })

    if (typeof (mobile_app) != 'undefined' && mobile_app == true) {
        $('#reject_mobile').hide();
    }

    $('.search_open').click(function () {       
        $('.search_open, .top>h1, .main_menu_anchor').fadeOut('fast',
		    function () {
		        $('.search_close').fadeIn('fast',
                   function () {
                       $('.search_container').show();
                       $('.search_container #s').animate({ "width": "97%" }, 500);
                   }
                );
		    }
		);
    });   

    $('.search_close').click(function () {
        $('.search_container #s').animate({ "width": "0px" }, 500,
            function () {
                $('.search_container,.search_close').hide();
                
                $('.search_open, .top>h1,.main_menu_anchor').fadeIn('fast');
                
        });   
    });

    //
    $('#btn_use_existing_img').click(function () {
        if ($('#btn_use_existing_img').html() == "ocultar medias existentes") {
            $('.use_existing_media ul.medias-album').animate({ "height": "0px" }, 500, function () {
                $('#btn_use_existing_img').html('usar medias existentes');
            });
        }
        else {
            $('.use_existing_media ul.medias-album').hide();
            $('.use_existing_media ul.medias-album').css('height','auto');
            var imHeight = $('.use_existing_media ul.medias-album').height();
            $('.use_existing_media ul.medias-album').css('height', '0px');
            $('.use_existing_media ul.medias-album').show();
            $('.use_existing_media ul.medias-album').animate({ "height": imHeight+"px" }, 500, function () {
                $('#btn_use_existing_img').html('ocultar medias existentes');
            });
        }                  
    });

   
    $('.use_existing_media ul li a.fancybox').unbind('click.fb').removeData('fancybox');

    $('.use_existing_media ul li a').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var parentLi = $(this).parents('li');
        var imgid = $(parentLi).find('.itemId').val();
        var imgsrc = $(parentLi).find('img').prop('src');
        var imgHref = $(parentLi).find('a').attr('href');
        var imgname = $(parentLi).find('img').attr("alt");
                       
        var item =       

        '<li>\
            <span class="mover" style="display: none;"></span>\
            <a class="fancybox fancybox.iframe" href="' + imgHref + '" rel="permalink" title="' + imgname + '">\
                <img src="' + imgsrc + '">\
            </a>\
            <input type="hidden" value="' + imgid + '" id="_fam_upload_id_' + imgid + '" name="_fam_upload_id_' + imgid + '">\
            <input type="hidden" value="1" id="_fam_upload_site_id_' + imgid + '" name="_fam_upload_site_id_' + imgid + '">\
            <input type="text" value="' + imgname + '" name="_fam_upload_name_' + imgid + '" id="_fam_upload_name_' + imgid + '">\
            <span title="Remover item" class="fam_delete_img">\
                <span></span>\
            </span>\
         </li>';      

       
        if ($('.fam_upload ul').length == 0) {
            $('.fam_upload').html('<ul></ul>');
        }
        var teste = $('.fam_upload ul');
        $('.fam_upload ul').append(item);

        $('.use_existing_media ul.medias-album').animate({ "height": "0px" }, 500, function () {
            $('#btn_use_existing_img').html('usar medias existentes');
            $('html, body').animate({ scrollTop: $('#_fam_upload_name_' + imgid).offset().top - 200}, 500);
        });
    });
    
    
});

$(window).load(function () {   
    AdjustforumShow();
    AdjustGaleria();
    AdjustNewsOptin();
});

function AdjustGaleria() {
    var maxHeight = 0;
    $('ul.galeriafoto li').each(function () {
        tempItemHeight = $(this).find('div.album').height() + $(this).find('div.album-info').height();
        if (tempItemHeight > maxHeight) {
            maxHeight = tempItemHeight;
        }
    });
    $('ul.galeriafoto li').css('min-height', maxHeight + "px");    
}

function AdjustNewsOptin() {
    $('.social_media_container .news_mail').width($('.social_media_container').width() - 74);
    $('.social_media_container .news_options').width($('.social_media_container').width() - 17);
}

window.onresize = function () {   
    AdjustGaleria();
    AdjustNewsOptin();
    
}



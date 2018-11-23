
var ajaxurl_original = ajaxurl;
var site_changed = false;


jQuery(document).ready(function ($) {

    function wpLinkSubmitPostSelect(event) {
        var linkAtts = wpLink.getAttrs();
        addSelectedItemToList(linkAtts.href, linkAtts.title);
        resetAndClosePostModal();
        event.preventDefault ? event.preventDefault() : event.returnValue = false;  // Trap any events
        event.stopPropagation();
        return false;
    }

    function closePostLinkSelectionWindow() {
        var isMCEFunction = wpLink.isMCE;
        wpLink.isMCE = function () { return false; }
        wpLink.textarea = $('#dummy_text_area');
        wpLink.close();
        wpLink.isMCE = isMCEFunction;
        wpLink.textarea = undefined;
    }

    function refreshRecentPost() {
        site_changed = false;        
        GetRecents();
    }

    function addSelectedItemToList(postlink, postname) {
        ajaxSite = getSelectedPostsiteSearch();
        var regex = /\/\d+\//;
        var regex_result = postlink.match(regex);
        var post_id = 0;
        if (regex_result != null && regex_result != 'undefined' && regex_result.length > 0) {
            post_id = regex_result[0].replace(/\//g, '');
            site_and_post_id = ajaxSite[0] + '-' + post_id;

            $('ul.selected_related_posts').append(
                '<li class="alternate">\
                <input type="hidden" name="fam_mu_related_post_' + site_and_post_id + '" class="item-permalink" value="' + site_and_post_id + '">\
                <span class="item-title">' + postname + '</span>\
                <span title="remover" onclick="javascript:$(this).parent().remove();" style="color: red;font-size: 18px;float:right;cursor:pointer" class="remove_related_post">x</span>\
            </li>');
        }
        else {
            alert('Desculpe, ocorreu um erro ao selecionar o post');
        }

    }

    function getSelectedPostsiteSearch() {
        ajaxsite = $('#related_posts_site_select').find(":selected").val();
        ajaxsite = ajaxsite.split('|');
        return ajaxsite;
    }

    function resetAndClosePostModal() {        
        resetWpLinkSearch();
        closePostLinkSelectionWindow();
    }

    function checkHandlerIsBinded(elemSelector, handlerName) {
        var selectedEventAlreadybinded = false;
        var ele = $(elemSelector);
        var btnevents = $._data($(elemSelector)[0], 'events');
        $(btnevents).each(function (item) {
            $(this.click).each(function () {
                if (typeof this.handler != 'undefined' && typeof this.handler.name != 'undefined') {
                    if (this.handler.name == handlerName) {
                        selectedEventAlreadybinded = true;
                       
                    }
                }
            });
        });

        return selectedEventAlreadybinded;
    }

    function clearWpLinkResults() {
        $('#link-selector #search-results ul').html('');
        $('#link-selector #most-recent-results ul').html('');
    }

    function resetWpLinkSearch() {
        ajaxurl = ajaxurl_original;
        clearWpLinkResults();
        refreshRecentPost();
        $('#fam_mu_hide_insert_link').remove();
    }


    $(document).ready(function () {
          
        $('select').change(function () {
            resetWpLinkSearch();
            site_changed = true;
        });

        $('#post_search_open_btn').click(function () {
            wpActiveEditor = true;
            ajaxurl = getSelectedPostsiteSearch()[1];
            
            $('head').append('<style id="fam_mu_hide_insert_link" type="text/css"> #link-options, #internal-toggle  {display:none !important;} .ui-widget-overlay{background:#000 !important;}</style>');
            wpLink.open();
            var itens = $('#most-recent-results ul li');
            
            ajaxurl = getSelectedPostsiteSearch()[1];
            clearWpLinkResults();
            refreshRecentPost();
            
            $('#search-panel').show();
            if (!checkHandlerIsBinded('#wp-link-submit', 'wpLinkSubmitPostSelect')) {
                $('#wp-link-submit').click(wpLinkSubmitPostSelect);
            }

            if (!checkHandlerIsBinded('#wp-link-cancel a', 'resetAndClosePostModal')) {
                $('#wp-link-cancel a').click(resetAndClosePostModal);
            }
            $('#wp-link-cancel a').click(resetAndClosePostModal);

            var titleBar = $('#wp-link').prev('div.ui-dialog-titlebar');
            var closeBtn = $(titleBar).find('a.ui-dialog-titlebar-close');            
            $(closeBtn).click(resetWpLinkSearch);

            return false;
        });
    });

    function GetRecents()
    {
        $('#most-recent-results .river-waiting').show();
		query = { action : 'wp-link-ajax',page : 0,'_ajax_linking_nonce' : $('#_ajax_linking_nonce').val()};         
        $.post( ajaxurl, query, function(r) { PopulateRecents(r, query); }, "json" );        
    }

    function PopulateRecents(results)
    {
       
        $('#search-results').hide();
        $('#most-recent-results').show();
        $('#most-recent-results ul').html('');
        $.each(results, function (key, value) {
            var r = value;
            var htmlLi = '<li class="alternate"><input type="hidden" class="item-permalink" value="' + r.permalink + '"><span class="item-title">' + r.title + '</span><span class="item-info">' + r.info + '</span></li>';
            $('#most-recent-results ul').append(htmlLi);
        });
        $('#most-recent-results .river-waiting').hide();
    }

});


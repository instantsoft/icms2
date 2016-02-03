var page_id = 0;
var page_controller = 'core';
var current_pallette = null;

$(function() {

    current_pallette = false;
    page_id = 0;

    var tree_url = $('#cp-widgets-layout').data('tree-url');

    $("#datatree").dynatree({

        onPostInit: function(isReloading, isError){
            var path = $.cookie('icms[widgets_tree_path]');
            if (!path) {path = '/core/core.0';}
            $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                if(status == "loaded") {
                    node.expand();
                }else if(status == "ok") {
                    node.activate();
                    node.expand();
                }
            });
        },

        onActivate: function(node){
            node.expand();
            $.cookie('icms[widgets_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
            widgetsSelectPage(node.data.key);
        },

        onLazyRead: function(node){
            node.appendAjax({
                url: tree_url,
                data: {
                    controller_name: node.data.key
                }
            });
        }

    });

    $( "#cp-widgets-list #accordion a" ).click(function(){
        if (current_pallette === $(this).attr('rel')) {return false;}
        current_pallette = $(this).attr('rel');
        $("#cp-widgets-list #accordion ul:visible").slideToggle().parent('div').find('a').find('span').html('&rarr;');
        $('span', this).html('&darr;');
        $(this).parent('div').find('ul').slideToggle();
    });

    $( "#cp-widgets-layout .position" ).sortable({
        items: "li:not(.disabled)",
        revert: true,
        opacity: 0.9,
        delay: 150,
        cancel: '.actions',
        connectWith: ".position",
        placeholder: 'placeholder',
        update: function(event, ui) {
            widgetsMarkTabbed();
            var pos = $(this).attr('rel');
            if (ui.item.attr('rel') == 'new'){
                var id = ui.item.data('id');
                ui.item.attr('rel', '');
                ui.item.data('widget-id', id);
                widgetsAdd(id, pos, ui.item);
            } else {
                widgetsSavePositionOrderings(pos);
            }
        }
    });

    $( "#cp-widgets-list ul li" ).draggable({
        connectToSortable: "#cp-widgets-layout .position",
        helper: "clone",
        revert: "invalid"
    });

    $( "ul, li" ).disableSelection();

    $( "#cp-widgets-list #accordion a" ).eq(0).trigger('click');

});

function widgetsSelectPage(key){

    key = key.split('.');

    var controller = key[0];
    var id = key[1];

    $('.cp_toolbar .edit').hide();
    $('.cp_toolbar .delete').hide();

    if (!isNaN(id)&&parseInt(id)==id){
        page_id = id;
        page_controller = controller;
    } else {
        page_id = null;
        page_controller = 'core';
        return false;
    }

    if (page_controller=='custom'){

        var delete_page_url = $('#cp-widgets-layout').data('delete-page-url');
        $('.cp_toolbar .delete').show();
        $('.cp_toolbar .delete a').attr('href', delete_page_url + '/' + page_id);

    }

    if (page_controller!='core'){
        var edit_page_url = $('#cp-widgets-layout').data('edit-page-url');
        $('.cp_toolbar .edit').show();
        $('.cp_toolbar .edit a').attr('href', edit_page_url + '/' + page_id);
    }

    widgetsLoad(page_id);

    return true;

}

function widgetsLoad(page_id){

    var load_url = $('#cp-widgets-layout').data('load-url');

    $('#cp-widgets-layout .position').html('');

    $.post(load_url, {page_id: page_id}, function(result){

        if (!result.is_exists){return;}

        for(var pos in result.scheme){

            for (var idx in result.scheme[pos]){

                var widget = result.scheme[pos][idx];
                var widget_dom = $(document.createElement('li')).attr('bind-id', widget.id).html(widget.title);

                if (widget.is_tab_prev){
                    widget_dom.addClass('is_tab_prev');
                }

                $('#cp-widgets-layout #pos-'+pos).append(widget_dom);

                if (widget.is_disabled) {
                    widget_dom.addClass('disabled');
                } else {
                    widgetAddActionButtons(widget_dom);
                }

                widgetsMarkTabbed();

            }

        }

        icms.events.run('admin_widgets_load', result);

    }, 'json');


}

function widgetsMarkTabbed(){
    $('#cp-widgets-layout .position li').removeClass('tabbed');
    $('#cp-widgets-layout .position li').each(function(){
        if ($(this).hasClass('is_tab_prev')){
            $(this).addClass('tabbed');
            $(this).prev().addClass('tabbed');
        }
    });
}

function widgetAddActionButtons(widget_dom){

    widget_dom.append( $('#actions-template').html() );

    $('.actions .edit', widget_dom).click(function(){
        var widget_id = $(this).parent('span').parent('li').attr('bind-id');
        widgetEdit(widget_id);
    });

    $('.actions .delete', widget_dom).click(function(){
        var widget_id = $(this).parent('span').parent('li').attr('bind-id');
        widgetDelete(widget_id);
    });

}

function widgetsAdd(id, position, widget_dom){

    var add_url = $('#cp-widgets-layout').data('add-url');

    var data = {
        widget_id: id,
        page_id: page_id,
        position: position
    };

    $.post(add_url, data, function(result){

        if (result.error){
            widget_dom.remove();
            return;
        }

        widget_dom.attr('bind-id', result.id);

        widgetAddActionButtons(widget_dom);

        widgetsSavePositionOrderings(position);

        widgetEdit(result.id);

        icms.events.run('admin_widgets_add', result);

    }, 'json');

}

function widgetEdit(id){

    var edit_url = $('#cp-widgets-layout').data('edit-url');

    icms.modal.openAjax(edit_url + '/' + id);

}

function widgetUpdated(widget){

    var widget_dom = $( "#cp-widgets-layout li[bind-id=" + widget.id + ']');

    widget_dom.html(widget.title);

    widgetAddActionButtons(widget_dom);

    if (widget.is_tab_prev){
        widget_dom.addClass('is_tab_prev');
    } else {
        widget_dom.removeClass('is_tab_prev');
    }

    widgetsMarkTabbed();

    icms.modal.close();

}

function widgetDelete(id){

    if (!confirm(LANG_CP_WIDGET_DELETE_CONFIRM)){return false;}

    var widget_dom = $( "#cp-widgets-layout li[bind-id=" + id + ']');

    var delete_url = $('#cp-widgets-layout').data('delete-url') + '/' + id;

    widget_dom.fadeOut(300, function(){
        widget_dom.remove();
        widgetsMarkTabbed();
    });

    $.post(delete_url, {}, function(){

    });

    return true;

}

function widgetsSavePositionOrderings(position){

    var list = $('#pos-' + position);

    var id_list = new Array();

    if ($('li:not(.disabled)', list).length==0) {return false;}

    $('li:not(.disabled)', list).each(function(){

        var id = $(this).attr('bind-id');

        id_list.push(id);

    });

    var reorder_url = $('#cp-widgets-layout').data('reorder-url');

    $.post(reorder_url, {position: position, items: id_list, page_id: page_id}, function(){

    });

    return true;

}

function widgetGetListItems(list_id, url){
	console.log('#'+list_id);
	console.log(url);
}
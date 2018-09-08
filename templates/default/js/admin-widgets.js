var page_id = 0;
var page_controller = 'core';
var current_pallette = null;

$(function() {

    $('#cp-widgets-select-template select').on('change', function (){
        window.location.href = $('#cp-widgets-select-template').data('current_url')+'?template_name='+$(this).val();
    });

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

    $( "#cp-widgets-list #accordion .section > a" ).click(function(){
        if (current_pallette === $(this).attr('rel')) {return false;}
        current_pallette = $(this).attr('rel');
        $("#cp-widgets-list #accordion ul:visible").slideToggle('fast').parent('div').find('a').find('span').html('&rarr;');
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
				ui.item.removeAttr('style');
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

    var sb = $('#datatree').parent();
    $(sb).after('<td id="slide_cell"></td>');
    $('#slide_cell').on('click', function (){
        if($(sb).is(':visible')){
            $(sb).hide();
            $(this).addClass('unslided');
        } else {
            $(sb).show();
            $(this).removeClass('unslided');
        }
    });
    $(window).on('resize', function(){
        if(!$(sb).is(':visible')){
            $('#slide_cell').addClass('unslided');
        }
    }).triggerHandler('resize');

    $('#cp-widgets-list #accordion .actions > a.delete').on('click', function(){
        widgetRemove($(this).closest('li').data('id'));
        return false;
    });

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

    if (page_controller != 'core' || page_id === '0'){
        var edit_page_url = $('#cp-widgets-layout').data('edit-page-url');
        $('.cp_toolbar .edit').show();
        $('.cp_toolbar .edit a').attr('href', edit_page_url + '/' + page_id);
    }

    widgetsLoad(page_id);

    return true;

}

function widgetsLoad(page_id){

    var load_url = $('#cp-widgets-layout').data('load-url');
    var template = $('#cp-widgets-layout').data('template');

    $('#cp-widgets-layout .position').html('');

    $.post(load_url, {page_id: page_id, template: template}, function(result){

        if (!result.is_exists){return;}

        for(var pos in result.scheme){

            for (var idx in result.scheme[pos]){

                createWidgetNode(result.scheme[pos][idx]);

            }

        }

        icms.events.run('admin_widgets_load', result);

    }, 'json');


}

function createWidgetNode(widget){

    var widget_dom = $(document.createElement('li')).attr('bind-id', widget.id).data('name', widget.name).html(widget.title);

    if (widget.is_tab_prev){
        widget_dom.addClass('is_tab_prev');
    }

    if (widget.device_types){
        widget_dom.addClass('device_restrictions');
        widget_dom.append('<span class="wd_device_types">'+widget.device_types.join(', ')+'</span>');
    }
    if (widget.languages){
        widget_dom.append('<span class="wd_languages">'+widget.languages.join(', ')+'</span>');
    }

    $('#cp-widgets-layout #pos-'+widget.position).append(widget_dom);
    if (widget.is_disabled) {
        widget_dom.addClass('disabled');
    } else {
        widgetAddActionButtons(widget_dom);
    }

    if (!widget.is_enabled) {
        widget_dom.addClass('hide').find('.actions .hide').attr('title', LANG_SHOW);
    }

    widgetsMarkTabbed();

    return widget_dom;

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
        return widgetEdit(widget_id);
    });

    $('.actions .delete', widget_dom).click(function(){
        var widget_id = $(this).parent('span').parent('li').attr('bind-id');
        return widgetDelete(widget_id);
    });

    $('.actions .hide', widget_dom).click(function(){
        var widget_id = $(this).parent('span').parent('li').attr('bind-id');
        return widgetToggle(widget_id);
    });

    $('.actions .copy', widget_dom).click(function(){
        var widget_id = $(this).parent('span').parent('li').attr('bind-id');
        return widgetCopy(widget_id);
    });

}

function widgetCopy(id){

    if (!confirm(LANG_CP_WIDGET_COPY_CONFIRM)){return false;}

    var copy_url = $('#cp-widgets-layout').data('copy-url') + '/' + id;

    var widget_dom = $( "#cp-widgets-layout li[bind-id=" + id + ']');

    $.post(copy_url, {}, function(response){

        if(response.error === true){ return false; }

        var new_widget_dom = createWidgetNode(response.widget);

        $(new_widget_dom).addClass('copied').on('mouseleave', function (){
            $(this).removeClass('copied');
        });

        widgetEdit(response.widget.id);

        icms.events.run('admin_widgets_copy', response.widget);

    }, 'json');

    return false;

}

function widgetToggle(id){

    var widget_dom = $( "#cp-widgets-layout li[bind-id=" + id + ']');

    var toggle_url = $('#cp-widgets-layout').data('toggle-url') + '/' + id;

    $.post(toggle_url, {}, function(result){
        if (result.error){ return; }
        if (result.is_on){
            widget_dom.removeClass('hide').find('.actions .hide').attr('title', LANG_HIDE);
        } else {
            widget_dom.addClass('hide').find('.actions .hide').attr('title', LANG_SHOW);
        }
    }, 'json');

    return false;
}

function widgetsAdd(id, position, widget_dom){

    var add_url = $('#cp-widgets-layout').data('add-url');
    var template = $('#cp-widgets-layout').data('template');

    var data = {
        widget_id: id,
        page_id: page_id,
        position: position,
        template: template
    };

    $.post(add_url, data, function(result){

        if (result.error){
            widget_dom.remove();
            return;
        }

        widget_dom.attr('bind-id', result.id);
        widget_dom.data('name', result.name);

        widgetAddActionButtons(widget_dom);

        widgetsSavePositionOrderings(position);

        widgetEdit(result.id);

        icms.events.run('admin_widgets_add', result);

    }, 'json');

}

function widgetEdit(id){

    var edit_url = $('#cp-widgets-layout').data('edit-url');
    var widget_dom = $( "#cp-widgets-layout li[bind-id=" + id + ']');

    icms.modal.openAjax(edit_url + '/' + id, undefined, function (){
        icms.modal.setCallback('close', function(){
            icms.forms.form_changed = false;
        });
        var h = 0, m = false;
        $('.modal_form .form-tabs .tab').each(function(indx, element){
            var th = +$(this).height();
            if (th > h){ h = th; m = true; }
        });
        if(m){
            $('.modal_form .form-tabs .tab').first().css({height: h+'px'});
            setTimeout(function(){ icms.modal.resize(); }, 10);
        }
    }, widget_dom.data('name'));

    return false;

}

function widgetUpdated(widget, result){

    var widget_dom = $( "#cp-widgets-layout li[bind-id=" + result.widget.id + ']');

    widget_dom.html(result.widget.title);

    if (result.widget.device_types){
        widget_dom.append('<span class="wd_device_types">'+result.widget.device_types.join(', ')+'</span>');
        widget_dom.addClass('device_restrictions');
    }

    if (result.widget.languages){
        widget_dom.append('<span class="wd_languages">'+result.widget.languages.join(', ')+'</span>');
    }

    widgetAddActionButtons(widget_dom);

    if (widget.is_tab_prev){
        widget_dom.addClass('is_tab_prev');
    } else {
        widget_dom.removeClass('is_tab_prev');
    }

    if (!result.widget.device_types){
        widget_dom.removeClass('device_restrictions');
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

    $.post(delete_url, {}, function(){});

    return false;

}

function widgetRemove(id){

    if (!confirm(LANG_CP_WIDGET_REMOVE_CONFIRM)){return false;}

    var widget_dom = $( "#cp-widgets-list #accordion li[data-id=" + id + ']');

    widget_dom.fadeOut(500, function(){

        widget_dom.remove();

        icms.modal.openAjax($('#cp-widgets-layout').data('files-url')+'/'+id+'/0', undefined, function (){

            $.post($('#cp-widgets-layout').data('remove-url')+'/'+id, {}, function(){});

        }, LANG_CP_PACKAGE_CONTENTS);

    });

    return false;

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
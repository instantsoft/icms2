var page_id = 0;
var page_controller = 'core';
var current_pallette = null;

var show_all_wd = $.cookie('icms[show_all_wd]');

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
            $('.breadcrumb-item.active').html(node.data.title);
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

    var last_pos;
    $( "#cp-widgets-layout .position" ).sortable({
        items: "li",
        cancel: ".disabled",
        opacity: 0.9,
        delay: 150,
        handle: '.title',
        connectWith: ".position",
        placeholder: 'placeholder',
        update: function(event, ui) {
            widgetsMarkTabbed();
            var pos = $(this).attr('rel');
            if(pos === '_copy' && ui.sender !== null){
                if(ui.item.attr('rel') !== 'new'){
                    if(last_pos.length < 1){
                        ui.sender.prepend(ui.item.clone());
                    }else{
                        last_pos.after(ui.item.clone());
                    }
                }
                ui.item.attr('data-bp_id', '0');
                return;
            }
            if(ui.sender !== null && ui.sender.attr('rel') === '_copy'){
                if(last_pos.length < 1){
                    ui.sender.prepend(ui.item.clone());
                }else{
                    last_pos.after(ui.item.clone());
                }
            }
            if(ui.item.attr('rel') === 'new'){
                var id = ui.item.attr('data-id');
                ui.item.attr('rel', '');
                ui.item.attr('data-widget-id', id);
				ui.item.removeAttr('style');
                widgetsAdd(id, pos, ui.item);
            }else{
                if(ui.item.parent().attr('rel') !== '_copy'){
                    widgetsSavePositionOrderings(pos);
                }
            }
        },
        start: function(event, ui) {
            last_pos = ui.item.prev();
        }
    }).disableSelection();

    $( "#cp-widgets-list ul li" ).draggable({
        connectToSortable: "#cp-widgets-layout .position",
        handle: '.title',
        helper: "clone",
        zIndex: 9999,
        revert: "invalid",
        start: function( event, ui ) {
            if($('#left-quickview.open').length > 0){
                $('#left-quickview .quickview-toggle.close').trigger('click');
            }
            let width = $('#accordion').width();
            $('.ui-draggable-dragging').css('min-width', width+'px');
        }
    }).disableSelection();

    $('#cp-widgets-layout').on('click', 'li .actions > a', function (){
        window[$(this).data('func')](this);
        return false;
    });

    $('#cp-widgets-list #accordion .actions > a.delete').on('click', function(){
        widgetRemove($(this).closest('li').attr('data-id'));
        return false;
    });

    $('#show_all_wd').on('click', function(e){
        show_all_wd = $(this).is(':checked') ? 1 : 0;
        $.cookie('icms[show_all_wd]', show_all_wd, {expires: 365, path: '/'});
        if(show_all_wd){
            $('#cp-widgets-layout .position li.is_hidden').removeClass('d-none');
        } else {
            $('#cp-widgets-layout .position li.is_hidden').addClass('d-none');
        }
    });
    if(show_all_wd == 0){
        $('#show_all_wd').prop('checked', false);
    }
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
        $('#cp-widgets-layout .position:not([rel="_copy"])').html('<i class="icon-lock icons font-xl d-block text-secondary"></i>');
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

    $('#cp-widgets-layout .position:not([rel="_copy"])').addClass('skeleton skeleton-loading');

    $.post(load_url, {page_id: page_id, template: template}, function(result){

        $('#cp-widgets-layout .position:not([rel="_copy"])').removeClass('skeleton skeleton-loading').html('');

        if (!result.is_exists){return;}

        for(var pos in result.scheme){

            for (var idx in result.scheme[pos]){
                createWidgetNode(result.scheme[pos][idx]);
            }

        }

        widgetsSavePositionOrderings('_unused');

        icms.events.run('admin_widgets_load', result);

    }, 'json');

}

function createWidgetNode(widget){

    var widget_dom = $(document.createElement('li'))
            .attr('bind-id', widget.bind_id)
            .attr('data-name', widget.name)
            .attr('data-widget-id', widget.widget_id)
            .attr('data-bp_id', widget.id)
            .html('<span class="title">'+widget.title+'</span>');

    if (widget.is_tab_prev){
        widget_dom.addClass('is_tab_prev');
    }

    if (widget.is_set_access){
        widget_dom.addClass('is_set_access');
    }

    if (widget.device_types){
        widget_dom.addClass('device_restrictions');
        var device_icons = '';
        for (var key in widget.device_type_names) {
            if(widget.device_type_names[key] === 'mobile'){
                widget.device_type_names[key] = 'smartphone';
            }
            device_icons = device_icons+'<i title="'+widget.device_types[key]+'" class="icon-screen-'+widget.device_type_names[key]+' icons mr-1"></i>';
        }
        widget_dom.append('<span class="wd_device_types mr-2">'+device_icons+'</span>');
    }
    if (widget.languages){
        var lang_icons = '';
        for (var key in widget.languages) {
            lang_icons = lang_icons+'<i title="'+widget.languages[key]+'" class="icon-lang-'+widget.languages[key]+' icon-svg  mr-1"></i>';
        }
        widget_dom.append(lang_icons);
    }

    $('#cp-widgets-layout #pos-'+widget.position).append(widget_dom);
    if (widget.is_disabled) {
        widget_dom.addClass('disabled');
    } else {
        widgetAddActionButtons(widget_dom);
    }
    if (widget.is_hidden) {
        widget_dom.addClass('is_hidden');
        if(show_all_wd == 0){
            widget_dom.addClass('d-none');
        }
    }
    if (!widget.is_enabled) {
        widget_dom.addClass('hide').find('.actions .hide').attr('title', LANG_SHOW);
    }

    if (widget.image_hint_path){
        widget_dom.append('<img src="'+widget.image_hint_path+'">');
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
    widget_dom.find('.actions').remove();
    if(widget_dom.find('img').length > 0){
        $('img', widget_dom).before($('#actions-template').html());
    } else {
        widget_dom.not('.disabled').append($('#actions-template').html());
    }
}

function widgetCopy(link){

    var id = parseInt(link) > 0 ? link : $(link).parents('li:first').attr('data-bp_id');

    if (!confirm(LANG_CP_WIDGET_COPY_CONFIRM)){return false;}

    var copy_url = $('#cp-widgets-layout').data('copy-url') + '/' + id;

    $.post(copy_url, {}, function(response){

        if(response.error === true){ return false; }

        var new_widget_dom = createWidgetNode(response.widget);

        $(new_widget_dom).addClass('copied').on('mouseleave', function (){
            $(this).removeClass('copied');
        });

        widgetEdit(response.widget.bind_id);

        icms.events.run('admin_widgets_copy', response.widget);

    }, 'json');

    return false;

}

function widgetToggle(link){

    var id = parseInt(link) > 0 ? link : $(link).parents('li:first').attr('data-bp_id');

    var widget_dom = $('#cp-widgets-layout li[data-bp_id=' + id + ']');

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
        widget_dom.attr('data-bp_id', result.bp_id);
        widget_dom.attr('data-name', result.name);

        widgetAddActionButtons(widget_dom);

        widgetsSavePositionOrderings(position);

        widgetEdit(result.id);

        icms.events.run('admin_widgets_add', result);

    }, 'json');

}

function widgetEdit(link){

    var id = parseInt(link) > 0 ? link : $(link).parents('li:first').attr('bind-id');

    var edit_url = $('#cp-widgets-layout').data('edit-url');
    var widget_dom = $('#cp-widgets-layout li[bind-id='+id+']');
    var template = $('#cp-widgets-layout').data('template');

    icms.modal.openAjax(edit_url + '/' + id, {template:template}, function (){}, widget_dom.attr('data-name'));

    return false;

}

function widgetUpdated(widget, result){

    var widget_dom = $( "#cp-widgets-layout li[bind-id=" + result.widget.id + ']');

    widget_dom.html('<span class="title">'+result.widget.title+'</span>');

    if (result.widget.device_types){
        var device_icons = '';
        for (var key in result.widget.device_type_names) {
            if(result.widget.device_type_names[key] === 'mobile'){
                result.widget.device_type_names[key] = 'smartphone';
            }
            device_icons = device_icons+'<i title="'+result.widget.device_types[key]+'" class="icon-screen-'+result.widget.device_type_names[key]+' icons mr-1"></i>';
        }
        widget_dom.append('<span class="wd_device_types">'+device_icons+'</span>');
        widget_dom.addClass('device_restrictions');
    }

    if (result.widget.languages){
        var lang_icons = '';
        for (var key in result.widget.languages) {
            lang_icons = lang_icons+'<i title="'+result.widget.languages[key]+'" class="icon-lang-'+result.widget.languages[key]+' icon-svg  mr-1"></i>';
        }
        widget_dom.append(lang_icons);
    }

    widgetAddActionButtons(widget_dom);

    if (widget.is_tab_prev){
        widget_dom.addClass('is_tab_prev');
    } else {
        widget_dom.removeClass('is_tab_prev');
    }

    if (result.widget.is_set_access){
        widget_dom.addClass('is_set_access');
    } else {
        widget_dom.removeClass('is_set_access');
    }

    if (!result.widget.device_types){
        widget_dom.removeClass('device_restrictions');
    }

    if (result.widget.image_hint_path){
        widget_dom.append('<img src="'+result.widget.image_hint_path+'">');
    }

    widgetsMarkTabbed();

    toastr.success(result.success_text);

    icms.modal.close();

}

function widgetDelete(link){

    var id = parseInt(link) > 0 ? link : $(link).parents('li:first').attr('data-bp_id');

    if (!confirm(LANG_CP_WIDGET_DELETE_CONFIRM)){return false;}

    var widget_dom = $( "#cp-widgets-layout li[data-bp_id=" + id + ']');

    var delete_url = $('#cp-widgets-layout').data('delete-url') + '/' + id;

    widget_dom.fadeOut(300, function(){
        widget_dom.remove();
        widgetsMarkTabbed();
    });

    $.post(delete_url, {}, function(data){
        if(data.errors === false && data.del_id){
            $('li[bind-id="'+data.del_id+'"]').remove();
        }
    }, 'json');

    return false;

}

function widgetRemove(id){

    if (!confirm(LANG_CP_WIDGET_REMOVE_CONFIRM)){return false;}

    var widget_dom = $( "#cp-widgets-list #accordion li[data-id=" + id + ']');

    widget_dom.fadeOut(500, function(){

        widget_dom.remove();

        icms.modal.openAjax($('#cp-widgets-layout').data('files-url')+'/'+id+'/0', undefined, function (){

            $.post($('#cp-widgets-layout').data('remove-url')+'/'+id, {}, function(result){
                toastr.success(result.success_text);
            }, 'json');

        }, LANG_CP_PACKAGE_CONTENTS);

    });

    return false;

}

function widgetsSavePositionOrderings(position){

    var list = $('#pos-' + position);
    var id_list = [];
    var id_now = {};

    var id_list = new Array();

    if($('li:not(.disabled)', list).length < 1){return false;}
    if(position === '_copy'){return true;}

    $('li', list).each(function(){
        var id = $(this).attr('bind-id');
        var bp_id = $(this).attr('data-bp_id');
        id_list.push({id:id, bp_id:bp_id, is_disabled: ($(this).hasClass('disabled') ? 1 : 0)});
        if(! +id || ! +bp_id){
            id_now[id] = true;
        }
    });

    $.post($('#cp-widgets-layout').data('reorder-url'), {position: position, items: id_list, page_id: page_id, template: $('#cp-widgets-layout').data('template')}, function(data){
        if(!data.errors){
            var id_new = {};
            if(data.new){
                for(var n in data.new){if(data.new.hasOwnProperty(n)){
                    list.find('li[bind-id="'+data.new[n]+'"]').each(function(){
                        if(!parseInt($(this).attr('data-bp_id'))){
                            $(this).attr('data-bp_id', n);
                            if(position !== '_unused'){
                                $(this).removeClass('hide');
                            }
                        }
                    });
                    id_new[data.new[n]] = true;
                }}
            }
            for(var id in id_now){if(id_now.hasOwnProperty(id)){
                if(typeof id_new[id] === 'undefined'){
                    $('li[bind-id="'+id+'"]').remove();
                }
            }}
        }
    }, 'json');

    return true;

}
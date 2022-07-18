var icms = icms || {};

$(document).ready(function(){

    for(var module in icms){
        if ( typeof(icms[module].onDocumentReady) === 'function' ) {
            icms[module].onDocumentReady();
        }
    }

    renderHtmlAvatar();

    $('.widget_tabbed').each(function(){

       $('.tabs .tab a', $(this)).click(function(){
           var wid = $(this).data('id');
           var block = $(this).parent('li').parent('ul').parent('.tabs').parent('.widget_tabbed');
           $('.body', block).hide();
           $('.links-wrap', block).hide();
           $('#widget-'+wid, block).show();
           $('#widget-links-'+wid, block).show();
           $('.tabs a', block).removeClass('active');
           $(this).addClass('active');
           return false;
       });

    });

	$('.messages.ajax-modal a').on('click', function(){
        $('#popup-manager').addClass('nyroModalMessage');
	});

});

icms.menu = (function ($) {

    this.allow_mobile_select = true;

    this.onDocumentReady = function(){

        $(document).on('click', function(event) {
            if ($(event.target).closest('.dropdown_menu').length) {
                $('.dropdown_menu > input').not($(event.target).closest('.dropdown_menu > input')).prop('checked', false);
                return;
            }
            $('.dropdown_menu > input').prop('checked', false);
        });

        if(this.allow_mobile_select){

            var dropdown = $('<select class="mobile_menu_select" />').appendTo("nav");
            $("<option value='/'></option>").appendTo(dropdown);

            $("nav .menu li > a").each(function() {
                var el = $(this);
                var nav_level = $("nav .menu").parents().length;
                var el_level = $(this).parents().length - nav_level;
                var pad = (el_level-2 + 1) >= 1 ? new Array(el_level-2 + 1).join('-') + ' ' : '';
                var attr = {
                    value   : el.attr('href'),
                    text    : pad + el.text()
                };
                if(window.location.pathname.indexOf(el.attr('href')) === 0){
                    attr.selected = true;
                }
                $("<option>", attr).appendTo(dropdown);
            });

            $("nav select.mobile_menu_select").change(function() {
                window.location = $(this).find("option:selected").val();
            });

            $(".tabs-menu").each(function() {

                var tabs = $(this);

                var dropdown = $('<select class="mobile_menu_select" />').prependTo(tabs);
                $("> ul > li > a", tabs).each(function() {
                    var el = $(this);
                    var attr = {
                        value   : el.attr('href'),
                        text    : el.text()
                    };
                    if(el.parent('li').hasClass('active')){
                        attr.selected = true;
                    }
                    $("<option>", attr).appendTo(dropdown);
                });

                $(dropdown).change(function() {
                    window.location = $(this).find("option:selected").val();
                });
            });
        }

        if($('div.widget.fixed_actions_menu').length){
            if ($('#breadcrumbs').length){
                $('#breadcrumbs').prepend($('div.widget.fixed_actions_menu'));
                $('div.widget.fixed_actions_menu').on('click', function (){
                    if($(this).hasClass('clicked')){ return; }
                    var __menu = $(this).addClass('clicked');
                    var hide_func = function (){
                        $(document).one('click', function(event) {
                            if ($(event.target).closest(__menu).length) { hide_func(); return; }
                            $(__menu).removeClass('clicked');
                        });
                    };
                    hide_func();
                });
            } else {
                $('div.widget.fixed_actions_menu').removeClass('fixed_actions_menu');
            }
        };

    };

    return this;

}).call(icms.menu || {},jQuery);

icms.forms = (function ($) {

    this.wysiwygs_insert_pool = {insert: {}, add: {}, init: {}, save: {}};
    this.submitted = false;
    this.form_changed = false;
    this.csrf_token = false;

    var _this = this;

    this.addWysiwygsInsertPool = function (field_name, callback){
        this.wysiwygs_insert_pool.insert[field_name] = callback;
    };

    this.addWysiwygsAddPool = function (field_name, callback){
        this.wysiwygs_insert_pool.add[field_name] = callback;
    };

    this.addWysiwygsInitPool = function (field_name, callback){
        this.wysiwygs_insert_pool.init[field_name] = callback;
    };

    this.addWysiwygsSavePool = function (field_name, callback){
        this.wysiwygs_insert_pool.save[field_name] = callback;
    };

    this.wysiwygBeforeSubmit = function (){
        for(var field_name in this.wysiwygs_insert_pool.save) {
            if(this.wysiwygs_insert_pool.save.hasOwnProperty(field_name)){
                if (typeof(this.wysiwygs_insert_pool.save[field_name]) === 'function') {
                    this.wysiwygs_insert_pool.save[field_name](field_name);
                }
            }
        }
        return this;
    };

    this.wysiwygInit = function (field_name){
        if (typeof(this.wysiwygs_insert_pool.init[field_name]) === 'function') {
            this.wysiwygs_insert_pool.init[field_name](field_name);
        }
        return this;
    };

    this.wysiwygInsertText = function (field_name, text){
        if (typeof(this.wysiwygs_insert_pool.insert[field_name]) === 'function') {
            this.wysiwygs_insert_pool.insert[field_name](field_name, text);
        } else {
            $('#'+field_name).val(text).focus();
        }
        return this;
    };

    this.wysiwygAddText = function (field_name, text){
        if (typeof(this.wysiwygs_insert_pool.add[field_name]) === 'function') {
            this.wysiwygs_insert_pool.add[field_name](field_name, text);
        } else {
            addTextToPosition($('#'+field_name), text);
        }
        return this;
    };

    this.getCsrfToken = function (){
        if(this.csrf_token === false){
            this.csrf_token = $('meta[name="csrf-token"]').attr('content');
        }
        return this.csrf_token;
    };

    this.setCsrfToken = function (csrf_token){
        this.csrf_token = csrf_token;
    };

    this.getFilterFormParams = function(form){

        var form_params = _this.toJSON(form);

        var o = {};
        for(var name in form_params){if(form_params.hasOwnProperty(name)){
            if(name === 'page'){ continue; }
            if(form_params[name] && form_params[name] !== '0'){
                o[name] = form_params[name];
            }
        }}

        return o;

    };

    this.initFilterForm = function(selector){

        var change = function (){

            var form = $(this.closest('form'));

            var sbutton = $(form).find('.buttons button[type = submit]');
            var spinner = $(form).find('.spinner.filter_loader');

            $(sbutton).prop('disabled', true);
            $(spinner).show();

            var o = _this.getFilterFormParams(form);

            if(Object.keys(o).length > 0 || $(form).find('.cancel_filter_link').length == 0){
                var submit_uri = $(form).attr('action');
            } else {
                var submit_uri = $(form).find('.cancel_filter_link').attr('href');
            }

            o.show_count = 1;

            var query_string = $.param(o);

            $.get(submit_uri+'?'+query_string, function(result){
                if(result.filter_link){
                    $(form).data('filter_link', result.filter_link);
                } else {
                    $(form).removeData('filter_link');
                }
                $(sbutton).val(result.hint).prop('disabled', false).find('span').text(result.hint);
                $(spinner).fadeOut('slow');
                icms.events.run('icms_content_filter_changed', form);
            }, 'json');

        };

        var delay = function () {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    change.apply(context, args);
                }, 500);
            };
        };

        $(selector).find('select, input[type=checkbox]').on('change', change);
        $(selector).find('input:not([type=checkbox]), textarea').on('input', delay());

        $(selector).find('.buttons button[type = submit]').on('click', function (){

            var form = $(this.closest('form'));

            var filter_link = $(form).data('filter_link');

            if(filter_link){
                window.location.href = filter_link;
                return false;
            }

            var submit_uri = $(form).attr('action');

            var o = _this.getFilterFormParams(form);

            var query_string = $.param(o);

            if(query_string.length > 0){
                window.location.href = submit_uri+'?'+query_string;
            } else {

                var cancel_filter_link = $(form).find('.cancel_filter_link').attr('href');

                if(!cancel_filter_link){
                    cancel_filter_link = submit_uri;
                }

                window.location.href = cancel_filter_link;
            }

            return false;

        });

    };

    this.initUnsaveNotice = function(){

        var init_data = {};

        $('form').each(function(i){
            init_data[i] = _this.toJSON($(this));
            $(this).attr('data-notice_id', i);
        });

        $(document).on('change', '.form-tabs input, .form-tabs select, .form-tabs textarea', function (e) {
            var form = $(this).closest('form');
            _this.form_changed = (JSON.stringify(init_data[form.attr('data-notice_id')]) !== JSON.stringify(_this.toJSON(form))) ? true : false;
        });
        $(document).on('submit', 'form', function () {
            icms.forms.submitted = true;
        });
        $(window).on('beforeunload', function (e) {
            if (icms.forms.form_changed && !icms.forms.submitted) {
                var e = e || window.event;
                var msg = LANG_SUBMIT_NOT_SAVE;
                if (e) {
                    e.returnValue = msg;
                }
                return msg;
            }
        });

    };

    this.initCollapsedFieldset = function(){
        $('.is_collapsed legend').on('click', function (){
            var _fieldset = $(this).closest('.is_collapsed');
            $(_fieldset).toggleClass('is_collapse do_expand');
            $.cookie('icms[fieldset_state]['+$(_fieldset).attr('id')+']', $(_fieldset).hasClass('do_expand'));
        });
        $('.is_collapsed').each(function (){
            if($(this).find('.field_error').length > 0 || $.cookie('icms[fieldset_state]['+$(this).attr('id')+']') === 'true'){
                $(this).addClass('do_expand').removeClass('is_collapse'); return;
            }
        });
    };

	this.toJSON = function(form) {
        _this.wysiwygBeforeSubmit();
        var o = {};
        var a = form.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
	};

    this.submit = function(selector){
        selector = selector || '.button-submit';
        icms.forms.submitted = true;
        $(selector).trigger('click');
    };

	this.initFieldsetChildList = function (form_id){
        $('#'+form_id+' .icms-form-tab__demand').each(function (){
            var demand_wrap = $(this).attr('href');
            var url = $(this).data('parent_url');
            $('#'+$(this).data('parent')).on('change', function (){
                var value = $(this).val();
                $.post(url, {value: value, form_id: form_id}, function(result){
                    $(demand_wrap).html(result);
                }, 'html');
            });
        });
    };

	this.updateChildList = function (child_id, url, value, current_value, filter_field_name) {

        var child_list = $('#' + child_id);

        if ($('#f_' + child_id + ' .loading').length === 0) {
            $('#f_' + child_id + ' label').append(' <div class="loading"></div>');
        }

        child_list.html('');

        current_value = current_value || '';

        if (!$.isArray(current_value)) {
            current_value = [current_value];
        }

        $.post(url, {value: value}, function (result) {

            for (var k in result) {
                if (result.hasOwnProperty(k)) {
                    if (typeof result[k].value !== 'undefined') {
                        var _value = result[k].value;
                        var title = result[k].title;
                    } else {
                        var _value = k;
                        var title = result[k];
                    }
                    child_list.append('<option value="' + _value + '"' + ($.inArray(_value, current_value) !== -1 ? ' selected' : '') + '>' + title + '</option>');
                }
            }

            $(child_list).trigger('chosen:updated');

            $('#f_' + child_id + ' .loading').remove();

            icms.events.run('icms_forms_updatechildlist', {
                result: result,
                child_id: child_id,
                value: value,
                current_value: current_value,
                filter_field_name: filter_field_name
            });

        }, 'json');

    };

    this.submitAjax = function(form, additional_params){

        icms.forms.submitted = true;

        var form_data = this.toJSON($(form));

        if(additional_params){
            $.extend(form_data, additional_params);
        }

        var url = $(form).attr('action');

        var submit_btn = $(form).find('.button-submit');

        $(submit_btn).prop('disabled', true);

        $.post(url, form_data, function(result){

            $(submit_btn).prop('disabled', false);

            if (result.errors === false){
                if ("callback" in result){

                    var params = result.callback.split('.');

                    var calling_func = window;
                    for(var id in params){
                        calling_func = calling_func[params[id]];
                    }

                    calling_func.apply(form, [form_data, result]);

                    return;
                }
                if (result.success_text){
                    icms.modal.alert(result.success_text);
                }
                if (result.redirect_uri){
                    window.location.href = result.redirect_uri;
                }
                return;
            }

            if (typeof(result.errors) === 'object'){

                $('.field_error', form).removeClass('field_error');
                $('.error_text', form).remove();

                for(var field_id in result.errors){
                    var id = field_id.replace(':', '_');
                    $('#f_'+id, form).addClass('field_error');
                    $('#f_'+id, form).prepend('<div class="error_text">' + result.errors[field_id] + '</div>');
                    $(form).find('ul.tabbed > li > a[href = "#'+$('#f_'+id, form).parents('.tab').attr('id')+'"]').trigger('click');
                }

                icms.events.run('icms_forms_submitajax', result);

                icms.modal.resize();

                icms.forms.submitted = false;

                return;

            }

        }, 'json');

        return false;

    };

    this.initSymbolCount = function (field_id, max, min){
        $('#f_'+field_id).append('<div class="symbols_count"><span class="symbols_num"></span><span class="symbols_spell"></span></div>');
        var symbols_count = $('#f_'+field_id+' > .symbols_count');
        var symbols_num   = $('.symbols_num', symbols_count);
        var symbols_spell = $('.symbols_spell', symbols_count);
        if(max){
            var type = 'left';
        } else {
            var type = 'total';
        }
        if(min){
            type = 'total';
        }
        var field_id_el = $('#'+field_id);

        $(symbols_num).on('click', function (){
            if(max){
                if(type === 'total'){
                    type = 'left';
                } else {
                    type = 'total';
                }
                render_symbols_count();
            }
        });
        var render_symbols_count = function (){
            var num = +$(field_id_el).val().length;
            if(!num){
                $(symbols_num).html(''); $(symbols_count).hide(); return;
            }
            if(min){ if(num < min){
                    $(symbols_num).addClass('overflowing_min');
                } else {
                    $(symbols_num).removeClass('overflowing_min');
            }}
            if(max && num > max){
                $(symbols_num).addClass('overflowing');
            } else {
                $(symbols_num).removeClass('overflowing');
            }
            if(type === 'total'){
                $(symbols_count).fadeIn();
                $(symbols_num).html(num);
                $(symbols_spell).html(spellcount(num, LANG_CH1, LANG_CH2, LANG_CH10));
            } else {
                $(symbols_count).fadeIn();
                if(num > max){
                    num = max;
                    $(field_id_el).val($(field_id_el).val().substr(0, max));
                }
                $(symbols_num).html((max - num));
                $(symbols_spell).html(spellcount(num, LANG_CH1, LANG_CH2, LANG_CH10)+' '+LANG_ISLEFT);
            }
        };
        $(field_id_el).on('input', render_symbols_count);
        icms.events.on('autocomplete_select', function(){ render_symbols_count(); });
        render_symbols_count();
    };

    this.getInputVal = function(el){
        if($(el).is(':checkbox,:radio')){
            var v = $(el+':checked').val();
            return typeof v === 'undefined' ? '0' : v;
        }
        return $(el).val();
    };
    this.inputNameToId = function(name){
        name = name.replace(/\:|\|/g,'_');
        return name;
    };
    this.inputNameToElName = function(name){
        name = name.split(':');
        return (typeof name !== 'string' && name.length > 1) ? name.shift()+'['+name.join('][')+']' :  name;
    };
    this.VDisDisplay = function(field_value, type){
        var display;
        if (Array.isArray(field_value) && field_value.length > 0) {
            for (var show_key in type) {
                if ($.inArray(type[show_key], field_value) !== -1) {
                    display = true;
                } else {
                    display = false;
                    break;
                }
            }
        } else {
            if (Array.isArray(field_value)) {
                field_value = '';
            }
            if ($.inArray(field_value, type) !== -1) {
                return true;
            } else {
                display = false;
            }
        }
        return display;
    };
    this.VDListeners = {};
    this.VDListenersInitialized = [];
    this.VDRules = {from:{},depends:{}};
    this.addVisibleDepend = function(form_id, field_id, rules){
        if(typeof this.VDRules.depends[form_id+'-'+field_id] === 'undefined'){ /* здесь все зависимости поля field_name */
            this.VDRules.depends[form_id+'-'+field_id] = rules; /* array('is_cats' => array('show' => array('1'))) */
        }else{
            $.extend(this.VDRules.depends[form_id+'-'+field_id], rules);
        }
        for(var f in rules){if(rules.hasOwnProperty(f)){
            if(typeof this.VDRules.from[form_id+'-'+f] === 'undefined'){this.VDRules.from[form_id+'-'+f] = {};}
            if(typeof this.VDRules.from[form_id+'-'+f][field_id] === 'undefined'){ /* здесь все, кто зависит от поля f */
                this.VDRules.from[form_id+'-'+f][field_id] = rules[f]; /* array('show' => array('1')) */
            }else{
                $.extend(this.VDRules.from[form_id+'-'+f][field_id], rules[f]);
            }
            if(typeof this.VDListeners[form_id+'-'+f] === 'undefined'){
                this.VDListeners[form_id+'-'+f] = '#'+form_id+' [name="'+this.inputNameToElName(f)+'"]';
                $('#'+form_id+' [name="'+this.inputNameToElName(f)+'"]').on('change input', function (){
                    for(var field in _this.VDRules.from[form_id+'-'+f]){if(_this.VDRules.from[form_id+'-'+f].hasOwnProperty(field)){ /* перебор тех, кто зависит от этого поля f */
                        var display = null; /* если не будет show */

                        for(var _from in _this.VDRules.depends[form_id+'-'+field]){if(_this.VDRules.depends[form_id+'-'+field].hasOwnProperty(_from)){ /* перебор тех, от кого зависит поле field */
                            if(typeof _this.VDRules.depends[form_id+'-'+field][_from]['show'] !== 'undefined'){
                                display = _this.VDisDisplay(_this.getInputVal('#'+form_id+' [name="'+_this.inputNameToElName(_from)+'"]'), _this.VDRules.depends[form_id+'-'+field][_from]['show']);
                                if(display === true){ break; }
                            }
                        }}

                        if(display === null){display = true;}

                        if(display){ /* скрытие сильнее показа */
                            for(var _from in _this.VDRules.depends[form_id+'-'+field]){if(_this.VDRules.depends[form_id+'-'+field].hasOwnProperty(_from)){ /* перебор тех, от кого зависит поле field */
                                if(typeof _this.VDRules.depends[form_id+'-'+field][_from]['hide'] !== 'undefined'){
                                    display = !_this.VDisDisplay(_this.getInputVal('#'+form_id+' [name="'+_this.inputNameToElName(_from)+'"]'), _this.VDRules.depends[form_id+'-'+field][_from]['hide']);
                                    if(display === false){ break; }
                                }
                            }}
                        }

                        if(display){
                            $('#f_'+_this.inputNameToId(field)).removeClass('hide_field').prev('.field_tabbed').removeClass('hide_field');
                        } else {
                            $('#f_'+_this.inputNameToId(field)).addClass('hide_field').prev('.field_tabbed').addClass('hide_field');
                        }

                    }}
                });
            }
        }}
        return this;
    };
    this.VDReInit = function(){
        for(var l in this.VDListeners){if(this.VDListeners.hasOwnProperty(l)){
            if(this.VDListenersInitialized.indexOf(this.VDListeners[l]) === -1) {
                $(this.VDListeners[l]).triggerHandler('change');
                $(this.VDListeners[l]).triggerHandler('input');
                this.VDListenersInitialized.push(this.VDListeners[l]);
            }
        }}
    };

	return this;

}).call(icms.forms || {},jQuery);

icms.events = (function ($) {

    this.listeners = {};

    this.on = function(name, callback){
        if (typeof(this.listeners[name]) == 'object'){
            this.listeners[name].push(callback);
        } else {
            this.listeners[name] = [callback];
        }
        return this;
    };

    this.run = function(name, params){
        params = params || {};
        for(var event_name in this.listeners[name]) {
            if(this.listeners[name].hasOwnProperty(event_name)){
                if (typeof(this.listeners[name][event_name]) == 'function') {
                    this.listeners[name][event_name](params);
                }
            }
        }
        return this;
    };

	return this;

}).call(icms.events || {},jQuery);

icms.pagebar = function(id, initial_page, has_next, is_modal){

    initial_page = initial_page || 1;

    var page = 1;

    var link = $(id);

    var showMore = function(){

        var list_wrap = $(link).prev();

        if(!has_next){
            if(initial_page > 1){
                return true;
            }
            $('body,html').animate({
                scrollTop: $(list_wrap).offset().top
                }, 500
            );
            return false;
        }

        $(link).addClass('show_spinner');

        page += 1;

        var post_params = $(link).data('url-params');
        post_params.page = page;

        $.post($(link).data('url'), post_params, function(data){

            var first_page_url = $(link).data('first-page-url');

            $(link).removeClass('show_spinner');

            if (!data.html) { return; }

            has_next = data.has_next;
            page = data.page;

            $(list_wrap).append(data.html);

            if(!has_next){
                $('span', link).html($('span', link).data('to-first'));
                $(link).attr('href', first_page_url);
            }

            var _sep = first_page_url.indexOf('?') !== -1 ? '&' : '?';

            if(!is_modal){
                window.history.pushState({link: first_page_url+_sep+'page='+page}, '', first_page_url+_sep+'page='+page);
            }

            if(is_modal){
                icms.modal.resize();
            }

        }, 'json');

        return false;

    };

    $(link).on('click', function (){
        return showMore();
    });

};

$.expr[':'].Contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});
function setCaretPosition(field, pos) {
    var $field = $(field);
    if ($field[0].setSelectionRange) {
        $field.focus();
        $field[0].setSelectionRange(pos, pos);
    } else if ($field[0].createTextRange) {
        var range = $field[0].createTextRange();
        range.collapse(true);
        range.moveEnd('character', pos);
        range.moveStart('character', pos);
        range.select();
    }
}
function getCaretPosition(field) {
    var $field = $(field);
    if($field.length){
        if (document.selection) {
            $field.focus();
            var Sel = document.selection.createRange();
            Sel.moveStart ('character', -$field.val().length);
            return Sel.text.length;
        } else {
            return $field[0].selectionStart || 0;
        }
    }
    return 0;
}
function addTextToPosition(field_id, text, spacer, spacer_stop){
	var field = $(field_id);
	var value = $(field).val();
	var pos = getCaretPosition(field);
    var value1 = value.substring(0, pos);
    var value2 = value.substring(pos, value.length);
    if(spacer){
        var check1 = function(spacer){ // проверка перед вставляемым
            if(value1.length >= spacer.length){
                if(value1.substring(pos - spacer.length, pos) !== spacer){
                    return true;
                }
            }
            return false;
        };
        var check2 = function(spacer){ // проверка после вставляемого текста
            if(value2.length >= spacer.length){
                if(value2.substring(0, spacer.length) !== spacer){
                    return true;
                }
            }
            return false;
        };
        var insert1 = true, insert2 = true;
        if(spacer_stop){
            for(var ss in spacer_stop){if(spacer_stop.hasOwnProperty(ss)){
                if(insert1 && (!spacer_stop[ss] || spacer_stop[ss] == 1) && !check1(ss)){
                    insert1 = false;
                }
                if(insert2 && (!spacer_stop[ss] || spacer_stop[ss] == 2) && !check2(ss)){
                    insert2 = false;
                }
            }}
        }

        if(insert1 && check1(spacer)){
            text = spacer+text;
        }
        if(insert2 && check2(spacer)){
            text += spacer;
        }
    }
    $(field).val(value1 + text + value2).trigger('input');
    setCaretPosition(field, pos+text.length);
    return false;
}
function toggleFilter(){
    var filter = $('.filter-panel');
    $('.filter-link', filter).toggle('fast');
    $('.filter-container', filter).slideToggle('fast');
}
function goBack(){
    window.history.go(-1);
}
function spellcount (num, one, two, many){
    if (num%10==1 && num%100!=11){
        str = one;
    } else if(num%10>=2 && num%10<=4 && (num%100<10 || num%100>=20)){
        str = two;
    } else {
        str = many;
    }
    return str;
}
function renderHtmlAvatar(wrap){
    wrap = wrap || document;
    $('div.default_avatar', wrap).each(function(){
        var a = this;
        var i = $('img', this);
        var isrc = $(i).attr('src');
        $(i).attr('src', '');
        $(i).attr('src', isrc);
        $(i).load(function() {
            var h = +$(this).height();
            $(a).css({
                'line-height': h+'px',
                'font-size': Math.round((h*0.625))+'px'
            });
        });
    });
}
function initMultyTabs(selector, tab_wrap_field){
    tab_wrap_field = tab_wrap_field || '.field';
    $(selector).each(function(indx, element){
        var tab = $(' > li > a', $(this));
        $(tab).closest('li').eq(0).addClass('active');
		$(tab).on('click', function() {
            var tab_field = $(this).attr('href');
			$(this).closest('li').addClass('active').siblings().removeClass('active');
            $(element).nextAll(tab_wrap_field+':lt('+$(tab).closest('li').length+')').hide();
            $(tab_field).show();
            return false;
		});
    });
}
function initTabs(selector){
    $(selector+' .tab').hide();
    $(selector+' .tab').eq(0).show();
    $(selector+' ul.tabbed > li').eq(0).addClass('active');
    $(selector+' ul.tabbed > li > a').click(function(){
        $(selector+' li').removeClass('active');
        $(this).parent('li').addClass('active');
        $(selector+' .tab').hide();
        $(selector+' '+$(this).attr('href')).show();
        icms.events.run('icms_tab_cliked', this);
        return false;
    });
    $(selector+' .field').each(function(indx, element){
        if($(element).hasClass('field_error')){
            $(selector+' ul.tabbed > li > a[href = "#'+$(element).parents('.tab').attr('id')+'"]').trigger('click');
        }
    });
    $('select.mobile_menu_select', selector).change(function() {
        $(selector+' ul.tabbed > li > a[href = "'+$(this).find("option:selected").val()+'"]').trigger('click');
    });
}
function insertJavascript(filepath, onloadCallback) {
    if ($('head script[src="'+filepath+'"]').length > 0){
        return;
    }
    var el = document.createElement('script');
    el.setAttribute('type', 'text/javascript');
    el.setAttribute('src', filepath);
    if (typeof(onloadCallback) == 'function') {
        el.setAttribute('onload', function() {
            onloadCallback();
        });
    }
    $('head').append(el);
}

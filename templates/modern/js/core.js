var icms = icms || {};

$(function(){
    for(var module in icms){
        if ( typeof(icms[module].onDocumentReady) === 'function' ) {
            icms[module].onDocumentReady();
        }
    }
});

icms.template = (function ($) {

    this.onDocumentReady = function(){
        this.initWidgetTabbed();
        this.initScrollTop();
        this.initTooltip();
        this.initCookieAlert();
    };

    this.initTooltip = function(){
        $('[data-toggle="tooltip"]').tooltip({trigger : 'hover'});
    };

    this.initWidgetTabbed = function(){
        $('.icms-widget__tabbed').each(function(){
            $('.nav.nav-tabs .nav-link', $(this)).click(function(){
                var wid = $(this).data('id');
                $('.links-wrap', $(this).closest('.card-header')).hide();
                $('#widget-links-'+wid).show();
            });
        });
    };

    this.initCookieAlert = function(){
        var block = $('#icms-cookiealert');
        if($(block).length === 0){ return; }
        if(localStorage.getItem('cookiealert_hide')){ return; }
        $(window).one('scroll', function() {
            $(block).addClass('show');
        });
        $('.acceptcookies', block).on('click', function(t) {
            localStorage.setItem('cookiealert_hide', 1);
            $(block).removeClass('show');
        });
    };

    this.initScrollTop = function(){
        var link = $('#scroll-top');
        if($(link).length === 0){ return; }
        if($(window).scrollTop() > 350){
            $(link).addClass('position-fixed');
        }
        $(window).on('scroll', function() {
            if($(this).scrollTop() > 350){
                $(link).addClass('position-fixed');
            } else {
                $(link).removeClass('position-fixed');
            }
        });
        $(link).on('click', function(t) {
            $('html, body').scrollTop(0);
            t.preventDefault();
        });
    };

    this.copyToBuffer = function (text, copy_text) {

        let textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.class = 'd-none';
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();

        copy_text = copy_text || 'Скопировано';

        if(typeof (toastr) === 'undefined'){
            alert(copy_text);
        } else {
            toastr.success(copy_text);
        }

        return false;
    };

    return this;

}).call(icms.template || {},jQuery);

icms.menu = (function ($) {

    this.onDocumentReady = function(){

        this.horizontalMenuAutoScroll();

        var device_type = $('body').data('device');

        if(device_type === 'desktop'){
            $('.icms-menu-hovered a.dropdown-toggle').on( 'click', function (e) {
                e.stopPropagation();
            });
        } else {

            $('.dropdown-menu a.dropdown-toggle').on( 'click', function (e) {
                var $el = $(this);
                $el.toggleClass('active-dropdown');
                if (!$( this ).next().hasClass('show')) {
                    $( this ).parents('.dropdown-menu').first().find('.show').removeClass('show');
                }
                $(this).next('.dropdown-menu').toggleClass('show');

                $(this).closest('li').toggleClass('show');

                $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function ( e ) {
                    $('.dropdown-menu .show').removeClass('show');
                    $el.removeClass('active-dropdown');
                });
                return false;
            });

            $('.nav-item.dropdown').each(function (){
                var link = $(this).find('>a');
                if(link.attr('href').charAt(0) === '/'){
                    $(this).find('>ul').append('<li class="dropdown-divider"></li>').append('<li class="nav-item"><a class="dropdown-item" href="'+link.attr('href')+'" >'+LANG_ALL+'</a></li>');
                }
            });

            $('.icms-user-menu').each(function (){
                $(this).on('show.bs.dropdown', function () {
                    $('body').addClass('overflow-hidden');
                }).on('hide.bs.dropdown', function () {
                    $('body').removeClass('overflow-hidden');
                });
                var avatar = $(this).find('.icms-user-menu__summary .icms-user-avatar').clone();
                $(avatar).on('click', function(){ return false; });
                var nickname = $('<span class="ml-3 text-white">'+$(this).find('.icms-user-menu__nickname').text()+'</span>');
                $(nickname).on('click', function(){ return false; });
                var header = $('<li class="bg-primary d-flex align-items-center m-0 h5 p-3"><button type="button" class="btn ml-auto text-white p-0"><svg viewBox="0 0 352 512" style="width: 1rem;"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></button></li>');
                $(header).prepend(nickname);
                $(header).prepend(avatar);
                $(this).find('.icms-user-menu__items').prepend(header);
            });

        }
    };

    this.horizontalMenuAutoScroll = function(){
        $('.mobile-menu-wrapper').each(function(){
            let active_link = $(this).find('.active');
            if(active_link.length > 0){
                $(active_link).get(0).scrollIntoView({block: "end", inline: "nearest"});
            }
        });
    };

    this.initSwipe = function(selector, params){
        return $(selector).slick($.extend({
            infinite: false,
            arrows: false,
            mobileFirst: true,
            variableWidth: false,
            responsive: [
                {breakpoint: 1024, settings: "unslick"},
                {breakpoint: 650, settings: {slidesToShow: 3, slidesToScroll: 3}},
                {breakpoint: 320, settings: {slidesToShow: 2, slidesToScroll: 2}}
            ]
        }, (params ? params : {})));
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
            if(form_params[name] && form_params[name] !== ''){
                o[name] = form_params[name];
            }
        }}

        return o;

    };

    this.initFilterForm = function(selector){

        var form = $(selector);

        var filter_panel = $(form).closest('.icms-filter-panel');

        var filter_link_open = $(filter_panel).find('.icms-filter-link__open');
        var filter_link_close = $(filter_panel).find('.icms-filter-link__close');

        $(filter_link_open).on('click', function (){
            $(this).addClass('d-none');
            $(filter_panel).find('.icms-filter-container').removeClass('d-none');
            return false;
        });
        $(filter_link_close).on('click', function (){
            $(filter_panel).find('.icms-filter-container').addClass('d-none');
            $(filter_link_open).removeClass('d-none');
            return false;
        });

        var change = function (){

            var sbutton = $(form).find('.buttons button[type = submit]');

            $(sbutton).prop('disabled', true).addClass('is-busy');

            var o = _this.getFilterFormParams(form);
            var submit_uri;

            if(Object.keys(o).length > 0 || $(form).find('.cancel_filter_link').length === 0){
                submit_uri = $(form).attr('action');
            } else {
                submit_uri = $(form).find('.cancel_filter_link').attr('href');
            }

            o.show_count = 1;

            var query_string = $.param(o);

            $.get(submit_uri+'?'+query_string, function(result){
                if(result.filter_link){
                    $(form).data('filter_link', result.filter_link);
                } else {
                    $(form).removeData('filter_link');
                }
                setTimeout(function (){
                    $(sbutton).removeClass('is-busy').val(result.hint).prop('disabled', false).find('span').text(result.hint);
                }, 200);
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

        $(form).find('select, input[type=checkbox]').on('change', change);
        $(form).find('input:not([type=checkbox]), textarea').on('input', delay());

        $(form).find('.buttons button[type = submit]').on('click', function (){

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

    this.initFormHelpers = function(){
        $('body').on('focus', '.field.ft_string input, .field.ft_text textarea', function (){
            $('.pattern_fields_panel').hide();
            $('.pattern_fields_panel_hint').show();
            $(this).closest('.field').find('.pattern_fields_panel_hint').hide();
            $(this).closest('.field').find('.pattern_fields_panel').show();
        });
        $('body').on('click', '.pattern_fields > ', function (){
            var spacer = $(this).closest('.hint').data('spacer') || false;
            var spacer_stop = $(this).closest('.hint').data('spacer_stop') || false;
            var id = $(this).closest('.icms-forms-pattern__fields').data('for_id');
            if (typeof(_this.wysiwygs_insert_pool.add[id]) === 'function') {
                _this.wysiwygs_insert_pool.add[id](id, $(this).text()); return false;
            }
            return addTextToPosition($('#'+id), $(this).text(), spacer, spacer_stop);
        });

        $('.auto_copy_value').on('click', function (){
            $(this).closest('.input-prefix-suffix').find('input').val($(this).data('value'));
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
            $(this).find('.button-submit').addClass('disabled is-busy');
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

    this.initCollapsedFieldset = function(form_id){
        $('#'+form_id).on('click', '.is_collapsed legend', function (){
            var _fieldset = $(this).closest('.is_collapsed');
            $(_fieldset).toggleClass('is_collapse do_expand');
            $.cookie('icms[fieldset_state]['+$(_fieldset).attr('id')+']', $(_fieldset).hasClass('do_expand'));
        });
        $('.is_collapsed', $('#'+form_id)).each(function (){
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
        $(selector).addClass('disabled is-busy').trigger('click');
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

        $('#f_' + child_id + ' label').addClass('loading');

        child_list.html('');

        current_value = current_value || '';

        if (!$.isArray(current_value)) {
            current_value = [current_value];
        }

        $.post(url, {value: value, filter_field_name: filter_field_name}, function (result) {

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

            $('#f_' + child_id + ' label').removeClass('loading');

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

        /**
         * Совместимость, передаётся в колбэк
         * @type coreforms.toJSON.o
         */
        var form_data = this.toJSON($(form));

        var submit_btn = $(form).find('.button-submit');

        $(submit_btn).prop('disabled', true).addClass('is-busy');

        var formData = new FormData(form);

        if(additional_params){
            for(var name in additional_params){
                formData.append(name, additional_params[name]);
            }
        }

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST',
            data: formData,
            success: function (result) {

                $('.field_error', form).removeClass('field_error');
                $('.is-invalid', form).removeClass('is-invalid');
                $('.invalid-feedback', form).remove();

                $(submit_btn).prop('disabled', false).removeClass('is-busy');

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
                    $('input[type=text], select, textarea', form).val('').triggerHandler('input');
                    if (result.success_text){
                        icms.modal.alert(result.success_text);
                    }
                    if (result.redirect_uri){
                        window.location.href = result.redirect_uri;
                    }
                    return;
                }

                if (typeof(result.errors) === 'object'){
                    if(result.message){
                        icms.modal.alert(result.message, 'danger');
                    }
                    for(var field_id in result.errors){
                        var id = field_id.replace(':', '_');
                        $('#'+id, form).addClass('is-invalid');
                        $('#f_'+id, form).addClass('field_error').append('<div class="invalid-feedback">' + result.errors[field_id] + '</div>');
                        $(form).find('ul.tabbed > li > a[href = "#'+$('#f_'+id, form).parents('.tab').attr('id')+'"]').trigger('click');
                    }

                    icms.events.run('icms_forms_submitajax', result);

                    icms.modal.resize();

                    icms.forms.submitted = false;

                    return;

                }
            },
            error: function (error) {
                $(submit_btn).prop('disabled', false).removeClass('is-busy');
                icms.modal.alert('<div>Status: '+error.status+'</div>'+error.responseText, 'danger');
            },
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    };

    this.initSymbolCount = function (field_id, max, min){
        $('#'+field_id).wrap("<div class='icms-form__symbols_count_wrap position-relative'></div>");
        $('#f_'+field_id+' .icms-form__symbols_count_wrap').append('<div class="symbols_count"><span class="symbols_num"></span></div>');
        var symbols_count = $('#f_'+field_id+' .symbols_count');
        var symbols_num   = $('.symbols_num', symbols_count);
        if(max){
            var type = 'left';
        } else {
            var type = 'total';
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
            } else {
                $(symbols_count).fadeIn();
                if(num > max){
                    num = max;
                    $(field_id_el).val($(field_id_el).val().substr(0, max));
                }
                $(symbols_num).html((max - num));
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
        /* здесь все зависимости поля field_id */
        if(typeof this.VDRules.depends[form_id+'-'+field_id] === 'undefined'){
            /* array('is_cats' => array('show' => array('1'))) */
            this.VDRules.depends[form_id+'-'+field_id] = rules;
        } else {
            $.extend(this.VDRules.depends[form_id+'-'+field_id], rules);
        }
        for(var f in rules){if(rules.hasOwnProperty(f)){
            if(typeof this.VDRules.from[form_id+'-'+f] === 'undefined'){this.VDRules.from[form_id+'-'+f] = {};}
            /* здесь все, кто зависит от поля f */
            if(typeof this.VDRules.from[form_id+'-'+f][field_id] === 'undefined'){
                /* array('show' => array('1')) */
                this.VDRules.from[form_id+'-'+f][field_id] = rules[f];
            } else {
                $.extend(this.VDRules.from[form_id+'-'+f][field_id], rules[f]);
            }
            if(typeof this.VDListeners[form_id+'-'+f] === 'undefined'){
                this.VDListeners[form_id+'-'+f] = '#'+form_id+' [name="'+this.inputNameToElName(f)+'"]';
                let field_obj = $(this.VDListeners[form_id+'-'+f]);
                let field_event = field_obj.attr('type') === 'text' ? 'input' : 'change';
                field_obj.on(field_event, function (){

                    let name = $(this).attr('name').replace(/\[/g, ':').replace(/\]/g, '');

                    /* перебор тех, кто зависит от этого поля name */
                    for(var field in _this.VDRules.from[form_id+'-'+name]){if(_this.VDRules.from[form_id+'-'+name].hasOwnProperty(field)){

                        /* если не будет show */
                        var display = null;

                        /* перебор тех, от кого зависит поле field */
                        for(var _from in _this.VDRules.depends[form_id+'-'+field]){if(_this.VDRules.depends[form_id+'-'+field].hasOwnProperty(_from)){
                            if(typeof _this.VDRules.depends[form_id+'-'+field][_from]['show'] !== 'undefined'){
                                display = _this.VDisDisplay(_this.getInputVal('#'+form_id+' [name="'+_this.inputNameToElName(_from)+'"]'), _this.VDRules.depends[form_id+'-'+field][_from]['show']);
                                if(display === true){ break; }
                            }
                        }}

                        if(display === null){display = true;}

                        /* скрытие сильнее показа */
                        if(display){
                            /* перебор тех, от кого зависит поле field */
                            for(var _from in _this.VDRules.depends[form_id+'-'+field]){if(_this.VDRules.depends[form_id+'-'+field].hasOwnProperty(_from)){
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

icms.head = (function ($) {

    var self = this;

    this.on_demand = {};
    this.loaded = {};
    this.loading = {};

    this.addJs = function(name, event){
        if(this.on_demand.js[name]){
            var filepath = this.on_demand.root + this.on_demand.js[name];
            if (this.loaded[filepath]){
                if(event){
                    icms.events.run(event);
                }
                return;
            }
            if (!this.loading[filepath]){
                this.loading[filepath] = true;
                var el = document.createElement('script');
                el.onload = function() {
                    if(event){
                        icms.events.run(event);
                    }
                    self.loaded[filepath] = true;
                };
                el.src = filepath;
                document.body.appendChild(el);
            }
        }
        return this;
    };

    this.addCss = function(name){
        if(this.on_demand.css[name]){
            var filepath = this.on_demand.root + this.on_demand.css[name];
            if (!this.loading[filepath]){
                this.loading[filepath] = true;
                var el = document.createElement('link');
                el.href = filepath;
                el.type = 'text/css';
                el.rel  = 'stylesheet';
                document.head.appendChild(el);
            }
        }
        return this;
    };

	return this;

}).call(icms.head || {},jQuery);

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
                if (typeof(this.listeners[name][event_name]) === 'function') {
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

        $(link).addClass('is-busy');

        page += 1;

        var post_params = $(link).data('url-params');
        post_params.page = page;

        $.post($(link).data('url'), post_params, function(data){

            var first_page_url = $(link).data('first-page-url');

            $(link).removeClass('is-busy');

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
function initMultyTabs(selector, tab_wrap_field){
    tab_wrap_field = tab_wrap_field || '.field';
    $(selector).each(function(indx, element){
        var tab = $(' > li > a', $(this));
        $(tab).eq(0).addClass('active');
		$(tab).on('click', function() {
            var tab_field = $(this).attr('href');
			$(this).addClass('active').closest('li').siblings().find('a').removeClass('active');
            $(element).nextAll(tab_wrap_field+':lt('+$(tab).closest('li').length+')').hide();
            $(tab_field).show();
            return false;
		});
    });
}

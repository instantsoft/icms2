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

    var dropdown = $("<select>").appendTo("nav");
    $("<option value='/'></option>").appendTo(dropdown);

    $("nav .menu li > a").each(function() {
        var el = $(this);
        var nav_level = $("nav .menu").parents().length;
        var el_level = $(this).parents().length - nav_level;
        var pad = new Array(el_level-2 + 1).join('-') + ' ';
        var attr = {
            value   : el.attr('href'),
            text    : pad + el.text()
        };
        if(window.location.pathname.indexOf(el.attr('href')) === 0){
            attr.selected = true;
        }
        $("<option>", attr).appendTo(dropdown);
    });

    $("nav select").change(function() {
        window.location = $(this).find("option:selected").val();
    });

    if ($('.tabs-menu').length){

        $(".tabs-menu").each(function() {

            var tabs = $(this);

            var dropdown = $("<select>").prependTo(tabs);
            $("> ul > li > a", tabs).each(function() {
                var el = $(this);
                var attr = {
                    value   : el.attr('href'),
                    text    : el.text()
                };
                if(window.location.pathname.indexOf(el.attr('href')) === 0){
                    attr.selected = true;
                }
                $("<option>", attr).appendTo(dropdown);
            });

            $(dropdown).change(function() {
                window.location = $(this).find("option:selected").val();
            });

        });

    }

	$('.messages.ajax-modal a').on('click', function(){
        $('#popup-manager').addClass('nyroModalMessage');
	});

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

});

icms.menu = (function ($) {

    this.onDocumentReady = function(){

        $(document).on('click', function(event) {
            if ($(event.target).closest('.dropdown_menu').length) {
                $('.dropdown_menu > input').not($(event.target).closest('.dropdown_menu > input')).prop('checked', false);
                return;
            }
            $('.dropdown_menu > input').prop('checked', false);
        });

    };

    return this;

}).call(icms.menu || {},jQuery);

icms.forms = (function ($) {

    this.submitted = false;
    this.form_changed = false;
    this.csrf_token = false;

    var _this = this;

    this.getCsrfToken = function (){
        if(this.csrf_token === false){
            this.csrf_token = $('meta[name="csrf-token"]').attr('content');
        }
        return this.csrf_token;
    };

    this.setCsrfToken = function (csrf_token){
        this.csrf_token = csrf_token;
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

	this.toJSON = function(form) {
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

    //====================================================================//

	this.updateChildList = function (child_id, url, value, current_value){

		var child_list = $('#'+child_id);

		if ($('#f_'+child_id+' .loading').length==0){
			$('#f_'+child_id+' label').append(' <div class="loading"></div>');
		}

		child_list.html('');

        current_value = current_value || '';

		$.post(url, {value: value}, function(result){

			for(var k in result){
                var __selected = (k === current_value ? ' selected' : '');
				child_list.append('<option value="'+k+'"'+__selected+'>'+result[k]+'</option>');
			}

            $(child_list).trigger('chosen:updated');

			$('#f_'+child_id+' .loading').remove();

            icms.events.run('icms_forms_updatechildlist', result);

		}, 'json');

	};

    //====================================================================//

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

            if (result.errors == false){
                if ("callback" in result){
                    window[result.callback](form_data, result); return;
                }
                if (result.success_text){
                    icms.modal.alert(result.success_text);
                }
                return;
            }

            if (typeof(result.errors)=='object'){

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
            return $(el+':checked').val();
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
    this.VDListeners = {};
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
                $('#'+form_id+' [name="'+this.inputNameToElName(f)+'"]').on('change', function (){
                    for(var field in _this.VDRules.from[form_id+'-'+f]){if(_this.VDRules.from[form_id+'-'+f].hasOwnProperty(field)){ /* перебор тех, кто зависит от этого поля f */
                        var display = null; /* если не будет show */

                        for(var _from in _this.VDRules.depends[form_id+'-'+field]){if(_this.VDRules.depends[form_id+'-'+field].hasOwnProperty(_from)){ /* перебор тех, от кого зависит поле field */
                            if(typeof _this.VDRules.depends[form_id+'-'+field][_from]['show'] !== 'undefined'){
                                if($.inArray(_this.getInputVal('#'+form_id+' [name="'+_this.inputNameToElName(_from)+'"]'), _this.VDRules.depends[form_id+'-'+field][_from]['show']) !== -1){
                                    display = true;
                                    break;
                                }else{
                                    display = false;
                                }
                            }
                        }}

                        if(display === null){display = true;}

                        if(display){ /* скрытие сильнее показа */
                            for(var _from in _this.VDRules.depends[form_id+'-'+field]){if(_this.VDRules.depends[form_id+'-'+field].hasOwnProperty(_from)){ /* перебор тех, от кого зависит поле field */
                                if(typeof _this.VDRules.depends[form_id+'-'+field][_from]['hide'] !== 'undefined'){
                                    if($.inArray(_this.getInputVal('#'+form_id+' [name="'+_this.inputNameToElName(_from)+'"]'), _this.VDRules.depends[form_id+'-'+field][_from]['hide']) !== -1){
                                        display = false;
                                        break;
                                    }else{
                                        display = true;
                                    }
                                }
                            }}
                        }

                        if(display){
                            $('#f_'+_this.inputNameToId(field)).show();
                        }else{
                            $('#f_'+_this.inputNameToId(field)).hide();
                        }

                    }}
                });
            }
        }}
        return this;
    };
    this.VDReInit = function(){
        for(var l in this.VDListeners){if(this.VDListeners.hasOwnProperty(l)){
            $(this.VDListeners[l]).triggerHandler('change');
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
    $('> select', selector).change(function() {
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

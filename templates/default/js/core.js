var icms = icms || {};

$(document).ready(function(){

    for(module in icms){
        if ( typeof(icms[module].onDocumentReady) == 'function' ) {
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
            $("ul > li > a", tabs).each(function() {
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

        $(document).on('change', '.form-tabs input, .form-tabs select, .form-tabs textarea', function (e) {
            icms.forms.form_changed = true;
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

    this.submit = function(){
        icms.forms.submitted = true;
        $('.button-submit').trigger('click');
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

    this.submitAjax = function(form){

        icms.forms.submitted = true;

        var form_data = this.toJSON($(form));

        var url = $(form).attr('action');

        var submit_btn = $(form).find('.button-submit');

        $(submit_btn).prop('disabled', true);

        $.post(url, form_data, function(result){

            $(submit_btn).prop('disabled', false);

            if (result.errors == false){
                if ("callback" in result){
                    window[result.callback](form_data, result);
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
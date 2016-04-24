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

    $("nav > .menu li > a").each(function() {
        var el = $(this);
        var nav_level = $("nav > .menu").parents().length;
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

            var dropdown = $("<select>").appendTo(tabs);
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

});

icms.forms = (function ($) {

    //====================================================================//

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

    //====================================================================//

    this.submit = function(){
        $('.button-submit').trigger('click');
    };

    //====================================================================//

	this.updateChildList = function (child_id, url, value){

		var child_list = $('#'+child_id);

		if ($('#f_'+child_id+' .loading').length==0){
			$('#f_'+child_id+' label').append(' <div class="loading"></div>');
		}

		child_list.html('');

		$.post(url, {value: value}, function(result){

			for(var k in result){
				child_list.append('<option value="'+k+'">'+result[k]+'</option>');
			}

            $(child_list).trigger('chosen:updated');

			$('#f_'+child_id+' .loading').remove();

            icms.events.run('icms_forms_updatechildlist', result);

		}, 'json');

	};

    //====================================================================//

    this.submitAjax = function(form){

        var form_data = this.toJSON($(form));

        var url = $(form).attr('action');

        $.post(url, form_data, function(result){

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
                }

                icms.events.run('icms_forms_submitajax', result);

                icms.modal.resize();

                return;

            }

        }, 'json');

        return false;

    };

    this.initSymbolCount = function (field_id, max){
        $('#f_'+field_id).append('<div class="symbols_count"><span class="symbols_num"></span><span class="symbols_spell"></span></div>');
        var symbols_count = $('#f_'+field_id+' > .symbols_count');
        var symbols_num   = $('.symbols_num', symbols_count);
        var symbols_spell = $('.symbols_spell', symbols_count);
        var type = 'total';
        $(symbols_num).on('click', function (){
            if(max){
                if(type === 'total'){
                    type = 'left';
                } else {
                    type = 'total';
                }
                $('#'+field_id).trigger('input');
            }
        });
        $('#'+field_id).on('input', function (){
            num = +$(this).val().length;
            if(type === 'total'){
                if(!num){
                    $(symbols_num).html(''); $(symbols_count).hide(); return;
                }
                $(symbols_count).fadeIn();
                $(symbols_num).html(num);
                $(symbols_spell).html(spellcount(num, LANG_CH1, LANG_CH2, LANG_CH10));
                if(max && num > max){
                    $(symbols_num).addClass('overflowing');
                } else {
                    $(symbols_num).removeClass('overflowing');
                }
            } else {
                $(symbols_count).fadeIn();
                if(num > max){
                    num = max;
                    $(this).val($(this).val().substr(0, max));
                }
                $(symbols_num).html((max - num)).removeClass('overflowing');
                $(symbols_spell).html(spellcount(num, LANG_CH1, LANG_CH2, LANG_CH10)+' '+LANG_ISLEFT);
            }
        }).triggerHandler('input');
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
    };

    this.run = function(name, params){
        params = params || {};

        for(event_name in this.listeners[name]) {
            if(this.listeners[name].hasOwnProperty(event_name)){
                if (typeof(this.listeners[name][event_name]) == 'function') {
                    this.listeners[name][event_name](params);
                }
            }
        }
    };

	return this;

}).call(icms.events || {},jQuery);

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
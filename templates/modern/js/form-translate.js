var icms = icms || {};

icms.translate = (function ($) {

    let self = this;

    this.url = '';

    this.onDocumentReady = function(){
        $('.multilanguage').not('.multilanguage-base').each(function (){
            if($(this).find('.form-control').val().length === 0){
                let lang = $(this).attr('rel');
                $(this).find('label').append('<a class="ml-2 text-muted" href="#" onclick="return icms.translate.run(this, \''+lang+'\');">'+LANG_TRANSLATE+'</a>');
            }
        });
    };

    this.run = function (link, to_lang){

        let lang_input = $(link).closest('.multilanguage').find('.form-control');

        let lang_input_id = $(lang_input).attr('id');

        let source = $('#f_'+lang_input_id.replace('_'+to_lang, ''));

        let lang_source = $(source).attr('rel');

        $.post(self.url, {tl: to_lang, sl: lang_source, q: $(source).find('.form-control').val()}, function(result){
            if(result.error){

                alert(result.message);

                return;
            }
            icms.forms.wysiwygInsertText(lang_input_id, result.translate);
            $(link).remove();
        }, 'json');

        return false;
    };

	return this;

}).call(icms.translate || {},jQuery);
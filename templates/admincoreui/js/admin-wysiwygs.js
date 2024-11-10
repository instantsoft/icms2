var icms = icms || {};
icms.adminWysiwygs = (function ($) {

    this.form_preset = {};
    this.options_url = '';
    this.preset_id = 0;

    let self = this;

    this.onDocumentReady = function(){

        this.form_preset = $('#form-preset');

        $('.form-tabs', this.form_preset).addClass('without-tabs');
        $('#tab-0', this.form_preset).remove();

        this.options_url = this.form_preset.data('wysiwyg_options_url');
        this.preset_id   = this.form_preset.data('preset_id');

        this.initOptions();
        this.initPanels();
    };

    this.initOptions = function(){
        $('#wysiwyg_name', this.form_preset).on('change', function(){

            let wysiwyg_name = $(this).val();

            if(!wysiwyg_name){ return; }

            $.post(self.options_url, {
                preset_id: self.preset_id,
                wysiwyg_name: wysiwyg_name
            }, function(data){

                $('#tab-basic + #tab-0', self.form_preset).remove();

                if (!data) { return; }

                if(data.error){
                    icms.modal.alert(data.message, 'ui_error'); return;
                }

                $('#tab-basic', self.form_preset).after($(data.html));

                $('.form-tabs', self.form_preset).find('.field.ft_string > input, .field.ft_text > textarea').each(function(){
                    $(this).trigger('input');
                });

                icms.events.run('loadwwoptions', data);

            }, 'json');
        }).triggerHandler('change');
    };

    this.initPanels = function(){
        $('.form-tabs', this.form_preset).on('input', '.field.ft_string > input, .field.ft_text > textarea', function (){
            if($(this).val().length === 0){ return; }
            let btns = $(this).val().split(' ');
            let panel = $(this).closest('.field').find('.pattern_fields');
            $('a', panel).show().css('background-color', '');
            for(let idx in btns){
                let btn = btns[idx].trim();
                if(btn.length < 2){
                    continue;
                }
                $('a:contains("'+btn+'")', panel).filter(function() {
                    let result = $(this).text().trim() === btn;
                    if(!result){
                        let matcher = new RegExp('^'+ btn);
                        if(matcher.test($(this).text())){
                            $(this).css('background-color', '#728994');
                        }
                    }
                    return result;
                }).hide();
            }
        });
    };

    return this;

}).call(icms.adminWysiwygs || {}, jQuery);
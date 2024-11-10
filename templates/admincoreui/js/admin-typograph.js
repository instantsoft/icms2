var icms = icms || {};
icms.adminTypograph = (function ($) {

    this.form_preset = {};
    this.options_url = '';
    this.preset_id = 0;

    let self = this;

    this.onDocumentReady = function(){

        this.form_preset = $('#form-preset');

        $('.form-tabs', this.form_preset).addClass('without-tabs');

        this.options_url = this.form_preset.data('options_url');
        this.preset_id   = this.form_preset.data('preset_id');

        this.initOptions();
    };

    this.initOptions = function(){

        let dispay_attrs = [];
        let show_attrs = [];

        $('#options_allowed_tags', this.form_preset).on('change', function(){

            show_attrs = [];
            dispay_attrs = [];

            $('#allowed_tags_attrs .tab-pane', self.form_preset).each(function(){
                dispay_attrs.push($(this).attr('id'));
            });

            let tags = $(this).val();

            if(!tags){
                tags = [];
            }

            $.post(self.options_url, {
                preset_id: self.preset_id,
                tags: tags
            }, function(data){

                if (!data) { return; }

                if(data.error){
                    icms.modal.alert(data.message, 'ui_error'); return;
                }

                let fsets = $(data.html);

                if($('#allowed_tags_attrs', self.form_preset).length === 0){
                    $('#tab-basic').after('<div id="allowed_tags_attrs" />');
                }

                $(fsets).each(function(){
                    if(!$(this).hasClass('tab-pane')){
                        return;
                    }
                    let id = $(this).attr('id');
                    if($('#'+id, self.form_preset).length === 0){
                        $('#allowed_tags_attrs', self.form_preset).append(this);
                    }
                    show_attrs.push(id);
                    dispay_attrs.push(id);
                });

                for (let k in dispay_attrs) {
                    if($.inArray(dispay_attrs[k], show_attrs) === -1) {
                        $('#'+dispay_attrs[k]).remove();
                    }
                }

            }, 'json');

        }).triggerHandler('change');
    };

    return this;

}).call(icms.adminTypograph || {}, jQuery);
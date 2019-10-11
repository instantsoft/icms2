var icms = icms || {};

icms.admin = (function ($) {

    this.onDocumentReady = function(){

        $('.need-scrollbar').each(function (){
            const ps = new PerfectScrollbar('#'+$(this).attr('id'));
        });

        $('.form-tabs').on('focus', '.field.ft_string > input, .field.ft_text > textarea', function (){
            $('.pattern_fields_panel').hide();
            $('.pattern_fields_panel_hint').show();
            $(this).closest('.field').find('.pattern_fields_panel_hint').hide();
            $(this).closest('.field').find('.pattern_fields_panel').show();
        });
        $('.form-tabs').on('click', '.pattern_fields > ', function (){
            var spacer = $(this).closest('.hint').data('spacer') || false;
            var spacer_stop = $(this).closest('.hint').data('spacer_stop') || false;
            return addTextToPosition($(this).closest('.field').find('input, textarea'), $(this).text(), spacer, spacer_stop);
        });

    };

    this.dbCardSpinner = function (el){
        return $(el).closest('.card').find('.db_spinner');
    };

    return this;

}).call(icms.admin || {},jQuery);
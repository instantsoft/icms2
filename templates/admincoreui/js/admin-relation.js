var icms = icms || {};
icms.adminRelation = (function ($) {

    this.onDocumentReady = function(){

        let title = $("input[id^=title]");

        let isTitleTyped = title.val() !== '';

        title.on('input', function(){
            isTitleTyped = true;
        });

        $('#child_ctype_id').on('change', function(){
           if (!isTitleTyped){

               let opt_title = $(this).find('option:selected').text().replace(/(.*): /gi, '');

               title.val(opt_title);
           }
        }).triggerHandler('change');

        $('#layout').on('change', function(){
           $('#tab-tab-opts fieldset').toggle( $(this).val() === 'tab' );
        }).triggerHandler('change');
    };

    return this;

}).call(icms.adminRelation || {},jQuery);
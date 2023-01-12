var icms = icms || {};
icms.adminRelation = (function ($) {

    this.onDocumentReady = function(){

        let isTitleTyped = $('#title').val() !== '';

        $('#title').on('input', function(){
            isTitleTyped = true;
        });

        $('#child_ctype_id').on('change', function(){
           if (!isTitleTyped){
               $('#title').val($(this).find('option:selected').text().replace(/(.*): /gi, ''));
           }
        }).triggerHandler('change');

        $('#layout').on('change', function(){
           $('#tab-tab-opts fieldset').toggle( $(this).val() === 'tab' );
        }).triggerHandler('change');
    };

    return this;

}).call(icms.adminRelation || {},jQuery);
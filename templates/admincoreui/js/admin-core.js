var icms = icms || {};

icms.admin = (function ($) {

    this.onDocumentReady = function(){
        $('.need-scrollbar').each(function (){
            const ps = new PerfectScrollbar('#'+$(this).attr('id'));
        });
    };

    this.dbCardSpinner = function (el){
        return $(el).closest('.card').find('.db_spinner');
    };

    return this;

}).call(icms.admin || {},jQuery);
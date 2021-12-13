var icms = icms || {};

icms.search = (function ($) {

    var form = $('#icms-search-form');

    this.onDocumentReady = function(){
        $(form).on('submit', function () {
            $(this).find('.button-submit').addClass('disabled is-busy');
        });
        $(form).find('select').on('change', function (){
            $(form).find('.button-submit').addClass('disabled is-busy').trigger('click');
        });
    };

    return this;

}).call(icms.search || {},jQuery);
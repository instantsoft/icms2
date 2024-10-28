var icms = icms || {};
icms.adminProp = (function ($) {

    this.onDocumentReady = function(){

        $('#tab-type #type').change(function () {
            if ($(this).val() === 'list' || $(this).val() === 'listbitmask') {
                $('#tab-values').show();
            } else {
                $('#tab-values').hide();
            }
            if ($(this).val() === 'color') {
                $('#f_is_in_filter').hide();
            } else {
                $('#f_is_in_filter').show();
            }
        });
    };

    return this;

}).call(icms.adminProp || {},jQuery);
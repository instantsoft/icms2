var icms = icms || {};
icms.adminProp = (function ($) {

    this.onDocumentReady = function(){

        $('#tab-type #type').change(function () {
            if ($(this).val() === 'list' || $(this).val() === 'list_multiple') {
                $('#tab-values').show();
            } else {
                $('#tab-values').hide();
            }
            if ($(this).val() === 'list_multiple') {
                $('#f_options_is_filter_multi').hide();
            } else {
                $('#f_options_is_filter_multi').show();
            }
            if ($(this).val() === 'number') {
                $('#tab-number').show();
            } else {
                $('#tab-number').hide();
            }
            if ($(this).val() === 'color') {
                $('#f_is_in_filter').hide();
            } else {
                $('#f_is_in_filter').show();
            }
        });

        $('#tab-type #type').trigger('change');
    };

    return this;

}).call(icms.adminProp || {},jQuery);
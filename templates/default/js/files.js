var icms = icms || {};

icms.files = (function ($) {

    //====================================================================//

    this.remove = function(file_id){

        $('#f_'+file_id+' #file_'+file_id).remove();
        $('#f_'+file_id+' #file_'+file_id+'_upload').show();
        $('#f_'+file_id+' #file_'+file_id+'_upload').show();
        $('#f_'+file_id+' input:hidden[name='+file_id+']').val('1');

    }

    //====================================================================//

	return this;

}).call(icms.files || {},jQuery);

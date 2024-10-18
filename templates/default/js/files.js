var icms = icms || {};

icms.files = (function ($) {

    this.url_delete = '';

    this.remove = function(file_id){

        $('#file_'+file_id).remove();
        $('#file_'+file_id+'_upload').show();
        $('#hidden_'+file_id).val('1');

        icms.events.run('icms_files_remove', file_id);
    };

    this.deleteByPath = function(path){
        $.post(this.url_delete, {path: path, csrf_token: icms.forms.getCsrfToken()}, function(result){
            if (result.error){
                alert(result.message);
                return false;
            }
        }, 'json');
    };

	return this;

}).call(icms.files || {},jQuery);
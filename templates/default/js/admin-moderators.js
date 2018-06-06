var icms = icms || {};

icms.adminModerators = (function ($) {

    this.url_submit = '';
    this.url_delete = '';

    //====================================================================//

    this.add = function(){

        var name = $('#user_email').val();

        if (name.length==0) { return false; }

        $('#ctype_moderators_add .field').hide();
        $('#ctype_moderators_add .loading-icon').show();

        $.post(this.url_submit, {name: name}, function(result){

            $('#user_email').val('');

            $('#ctype_moderators_add .field').show();
            $('#ctype_moderators_add .loading-icon').hide();

            if (result.error){
                alert(result.message);
                return false;
            }
            $('#ctype_moderators_list').show();
            $('#ctype_moderators_list #datagrid tbody').append(result.html);

            $('#ctype_moderators_list #datagrid tr').removeClass('odd');
            $('#ctype_moderators_list #datagrid tr:odd').addClass('odd');

            icms.modal.bind('a.ajax-modal');

            icms.events.run('admin_moderators_add', result);

        }, 'json');

        return false;

    };

    //====================================================================//

    this.cancel = function(id){

        var list_item = $('#ctype_moderators_list #moderator-'+id);

        $('a.delete, a.view', list_item).hide();
        $('.loading-icon', list_item).show();

        $.post(this.url_delete, {id: id}, function(result){

            if (result.error){
                $('.ajaxlink', list_item).show();
                $('.loading-icon', list_item).hide();
                return false;
            }

            list_item.fadeOut('fast', function(){
                $(this).remove();
                $('#ctype_moderators_list #datagrid tr').removeClass('odd');
                $('#ctype_moderators_list #datagrid tr:odd').addClass('odd');
                if (!$('#ctype_moderators_list #datagrid tbody tr').length){
                    $('#ctype_moderators_list').hide();
                }
                icms.events.run('admin_moderators_cancel', result);
            });

        }, 'json');

    };

    //====================================================================//

	return this;

}).call(icms.adminModerators || {},jQuery);

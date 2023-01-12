var icms = icms || {};

icms.adminModerators = (function ($) {

    this.url_submit = '';
    this.url_delete = '';
    this.url_autocomplete = '';

    var _this = this;

    this.onDocumentReady = function(){
        var cache = {};
        $( "#user_email" ).autocomplete({
            minLength: 2,
            delay: 500,
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }
                $.getJSON(_this.url_autocomplete, request, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });
            }
        });
        var ctype_moderators_add = $('#ctype_moderators_add');
        this.url_submit = $(ctype_moderators_add).data('url_submit');
        this.url_delete = $(ctype_moderators_add).data('url_delete');;
        this.url_autocomplete = $(ctype_moderators_add).data('url_autocomplete');;
    };

    this.add = function(btn){

        var name = $('#user_email').val();

        if (name.length === 0) { return false; }

        $(btn).addClass('is-busy');

        $.post(this.url_submit, {name: name}, function(result){

            $('#user_email').val('');

            $(btn).removeClass('is-busy');

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

	return this;

}).call(icms.adminModerators || {},jQuery);

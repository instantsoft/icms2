var icms = icms || {};

icms.adminModerators = (function ($) {

    this.url_submit = '';
    this.url_delete = '';
    this.url_autocomplete = '';
    this.ctype_moderators_list = {};

    let self = this;

    this.onDocumentReady = function(){
        this.ctype_moderators_list = $('#ctype_moderators_list');
        let ctype_moderators_add = $('#ctype_moderators_add');
        let cache = {};
        $("#user_email", ctype_moderators_add).autocomplete({
            minLength: 2,
            delay: 500,
            source: function(request, response) {
                let term = request.term;
                if (term in cache) {
                    response( cache[ term ] );
                    return;
                }
                $.getJSON(self.url_autocomplete, request, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });
            }
        });
        this.url_submit = ctype_moderators_add.data('url_submit');
        this.url_delete = ctype_moderators_add.data('url_delete');;
        this.url_autocomplete = ctype_moderators_add.data('url_autocomplete');
        $('#submit', ctype_moderators_add).on('click', function(){
            return self.add(this);
        });
        this.ctype_moderators_list.on('click', '.actions > .delete', function(){
            return self.cancel(this);
        });
    };

    this.add = function(btn){

        let name = $('#user_email').val();

        if (name.length === 0) { return false; }

        $(btn).addClass('is-busy');

        $.post(this.url_submit, {name: name}, function(result){

            $('#user_email').val('');

            $(btn).removeClass('is-busy');

            if (result.error){
                alert(result.message);
                return false;
            }
            self.ctype_moderators_list.show();
            $('#datagrid tbody', self.ctype_moderators_list).append(result.html);

            icms.modal.bind('a.ajax-modal');

            icms.events.run('admin_moderators_add', result);

        }, 'json');

        return false;
    };

    this.cancel = function(btn){

        let id = $(btn).data('user_id');

        let list_item = $('#moderator-'+id, self.ctype_moderators_list);

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
                if (!$('#datagrid tbody tr', self.ctype_moderators_list).length){
                    self.ctype_moderators_list.hide();
                }
                icms.events.run('admin_moderators_cancel', result);
            });

        }, 'json');

        return false;
    };

    return this;

}).call(icms.adminModerators || {},jQuery);
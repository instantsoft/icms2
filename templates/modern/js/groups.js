var icms = icms || {};

icms.groups = (function ($) {

    this.url_submit = '';
    this.url_delete = '';

    this.onDocumentReady = function() {
        $('.ajax-request').on('click', function(){
            var current_user = $(this).closest('.item');
            $(current_user).find('.dropdown > button').addClass('loading').css('background-image', false);
            $.post($(this).attr('href'), {}, function(result){
                if (result.error){
                    return false;
                }
                if ('callback' in result){
                    return window[result.callback](current_user, result);
                }
                $(current_user).fadeOut();
            }, 'json');
            return false;
        });
	};

    this.addRole = function(){

        var role = $('#role_input').val();
        if (role.length==0) { return false; }

        $('#role-submit').addClass('is-busy');

        $.post(this.url_submit, {role: role}, function(result){

            $('#role_input').val('');

            $('#role-submit').removeClass('is-busy');

            if (result.error){
                icms.modal.alert(result.message);
                return false;
            }

            $('#group_roles_list').append(result.html);

        }, 'json');

        return false;

    };

    this.submitRole = function(id){

        var list_item = $('#group_roles_list #role-'+id);

        var role = $('input.input', list_item).val();
        if (role.length==0) { return false; }

        $('.icms-group-roles__save', list_item).addClass('is-busy');

        $.post(this.url_submit, {role: role, role_id: id}, function(result){

            $('.icms-group-roles__save', list_item).removeClass('is-busy');

            if (result.error){
                icms.modal.alert(result.message);
                return false;
            }

            $('.role_title', list_item).html(result.role);

            $('.role_title', list_item).removeClass('d-none');
            $('.role_title_edit', list_item).addClass('d-none')

        }, 'json');

        return false;

    };

    this.deleteRole = function(id){

        var list_item = $('#group_roles_list #role-'+id);

        $('.icms-group-roles__delete', list_item).addClass('is-busy');

        $.post(this.url_delete, {role_id: id}, function(result){

            if (result.error){
                $('.icms-group-roles__delete', list_item).removeClass('is-busy');
                return false;
            }

            list_item.fadeOut(300, function(){
                $(this).remove();
            });

        }, 'json');

    };

    this.editRole = function(id){

        var list_item = $('#group_roles_list #role-'+id);

        $('.role_title', list_item).addClass('d-none');
        $('.role_title_edit', list_item).removeClass('d-none');

    };

    this.addStaff = function(){

        var name = $('#staff-username').val();

        if (name.length==0) { return false; }

        $('#group_staff_add #staff-submit').addClass('is-busy');

        $.post(this.url_submit, {name: name}, function(result){

            $('#staff-username').val('');

            $('#group_staff_add #staff-submit').removeClass('is-busy');

            if (result.error){
                icms.modal.alert(result.message);
                return false;
            }

            $('#group_staff_list').append(result.html);

        }, 'json');

        return false;

    };

    this.deleteStaff = function(id){

        var list_item = $('#group_staff_list #staff-'+id);

        $('.icms-group-sraff__delete', list_item).addClass('is-busy');

        $.post(this.url_delete, {staff_id: id}, function(result){

            if (result.error){
                $('.icms-group-sraff__delete', list_item).removeClass('is-busy');
                return false;
            }

            list_item.fadeOut(300, function(){
                $(this).remove();
            });

        }, 'json');

    };

	return this;

}).call(icms.groups || {},jQuery);
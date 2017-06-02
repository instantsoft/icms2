var icms = icms || {};

icms.groups = (function ($) {

    this.url_submit = '';
    this.url_delete = '';

    this.onDocumentReady = function() {
        $('.ajax-request').on('click', function(){
            var current_user = $(this).closest('.item');
            $(current_user).find('.group_menu_title').addClass('loading').css('background-image', false);
            $('body').trigger('click');
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

        $('#role-submit').hide();
        $('#group_role_add .loading-icon').show();

        $.post(this.url_submit, {role: role}, function(result){

            $('#role_input').val('');

            $('#role-submit').show();
            $('#group_role_add .loading-icon').hide();

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

        $('.ajaxlink', list_item).hide();
        $('.loading-icon', list_item).show();

        $.post(this.url_submit, {role: role, role_id: id}, function(result){

            $('.ajaxlink', list_item).show();
            $('.loading-icon', list_item).hide();

            if (result.error){
                icms.modal.alert(result.message);
                return false;
            }

            $('.role_title', list_item).html(result.role);

            $('.role_title', list_item).show();
            $('.role_title_edit', list_item).hide();

        }, 'json');

        return false;

    };

    this.deleteRole = function(id){

        var list_item = $('#group_roles_list #role-'+id);

        $('.ajaxlink', list_item).hide();
        $('.loading-icon', list_item).show();

        $.post(this.url_delete, {role_id: id}, function(result){

            if (result.error){
                $('.ajaxlink', list_item).show();
                $('.loading-icon', list_item).hide();
                return false;
            }

            list_item.fadeOut(500, function(){
                $(this).remove();
            });

        }, 'json');

    };

    this.editRole = function(id){

        var list_item = $('#group_roles_list #role-'+id);

        $('.role_title', list_item).hide();
        $('.role_title_edit', list_item).show();

    };

    this.addStaff = function(){

        var name = $('#staff-username').val();

        if (name.length==0) { return false; }

        $('#group_staff_add #staff-submit').hide();
        $('#group_staff_add .loading-icon').show();

        $.post(this.url_submit, {name: name}, function(result){

            $('#staff-username').val('');

            $('#group_staff_add #staff-submit').show();
            $('#group_staff_add .loading-icon').hide();

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

        $('.ajaxlink', list_item).hide();
        $('.loading-icon', list_item).show();

        $.post(this.url_delete, {staff_id: id}, function(result){

            if (result.error){
                $('.ajaxlink', list_item).show();
                $('.loading-icon', list_item).hide();
                return false;
            }

            list_item.fadeOut(500, function(){
                $(this).remove();
            });

        }, 'json');

    };

	return this;

}).call(icms.groups || {},jQuery);
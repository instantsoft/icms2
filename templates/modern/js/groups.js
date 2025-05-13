var icms = icms || {};

icms.groups = (function ($) {

    let self = this;

    this.url_submit = '';
    this.url_delete = '';
    this.group_staff_list = {};
    this.group_roles_list = {};

    this.onDocumentReady = function() {
        this.group_staff_list = $('#group_staff_list');
        this.group_roles_list = $('#group_roles_list');
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
        $('#staff-submit').on('click', function(){
            return self.addStaff(this);
        }).prop('disabled', false);
        $('#role-submit').on('click', function(){
            return self.addRole();
        });
        this.group_staff_list.on('click', '.icms-group-sraff__delete', function(){
            return self.deleteStaff(this);
        });
        self.group_roles_list.on('click', '.icms-group-roles__save', function(){
            return self.submitRole($(this).data('id'));
        }).on('click', '.icms-group-roles__edit', function(){
            return self.editRole($(this).data('id'));
        }).on('click', '.icms-group-roles__delete', function(){
            return self.deleteRole($(this).data('id'));
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

            self.group_roles_list.append(result.html);

        }, 'json');

        return false;
    };

    this.submitRole = function(id){

        let list_item = $('#role-'+id, this.group_roles_list);

        let role = $('input.input', list_item).val();
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

        let list_item = $('#role-'+id, this.group_roles_list);

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

        return false;
    };

    this.editRole = function(id){

        let list_item = $('#role-'+id, this.group_roles_list);

        $('.role_title', list_item).addClass('d-none');
        $('.role_title_edit', list_item).removeClass('d-none');
        return false;
    };

    this.addStaff = function(btn){

        let name = $('#staff-username').val();

        if (name.length==0) { return false; }

        $(btn).addClass('is-busy');

        $.post(this.url_submit, {name: name}, function(result){

            $('#staff-username').val('');

            $(btn).removeClass('is-busy');

            if (result.error){
                icms.modal.alert(result.message);
                return false;
            }

            self.group_staff_list.append(result.html);

        }, 'json');

        return false;
    };

    this.deleteStaff = function(link){

        let id = $(link).data('id');

        let list_item = $('#staff-'+id, this.group_staff_list);

        $(link).addClass('is-busy');

        $.post(this.url_delete, {staff_id: id}, function(result){

            if (result.error){
                $(link).removeClass('is-busy');
                return false;
            }

            list_item.fadeOut(300, function(){
                $(this).remove();
            });

        }, 'json');

        return false;
    };

    return this;

}).call(icms.groups || {},jQuery);
var icms = icms || {};

icms.groups = (function ($) {

    this.url_submit = '';
    this.url_delete = '';

    //====================================================================//

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
                alert(result.message);
                return false;
            }

            $('#group_staff_list').append(result.html);

        }, 'json');

        return false;
        
    }

    //====================================================================//

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

    }

    //====================================================================//

	return this;

}).call(icms.groups || {},jQuery);

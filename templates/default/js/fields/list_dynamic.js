var icms = icms || {};
icms.dynamicList = function(field_id, element_name, current_values, single_select){

    single_select = single_select || 0;

    var wrap = $('#list_wrap_'+field_id);

    var addValue = function (){
        $('.add_list select', wrap).html($('.key_items_list', wrap).html()).show();
        $('.add_list', wrap).show();
        $('.add_link', wrap).hide();
        return false;
    };

    var cancelValue = function (){
        $('.add_list', wrap).hide();
        $('.add_link', wrap).show();
        return false;
    };

    var unsetValue = function (link){
        var wrap_value = $(link).parent('span').parent('div');
        if(single_select > 0){
            $('.key_items_list option[id=key_option_'+$(wrap_value).data('field')+']', wrap).prop('disabled', false);
        }
        $(wrap_value).remove();
        cancelValue();
        return false;
    };

    var submitValue = function (data){

        if (typeof(data) == 'undefined') {
            data = {field: false, field_value: false};
        }

        if (data.field){
            var field = data.field;
        } else {
            var field = $('.add_list select', wrap).val();
        }

        var list_template = $('.list_template', wrap).clone(true).removeClass('list_template');

        var field_title = $('.key_items_list option[value='+field+']', wrap).html();
        if(single_select > 0){
            $('.key_items_list option[id=key_option_'+field+']', wrap).prop('disabled', true);
        }

        $('.title', list_template).append(field_title);
        $('.to select', list_template).html( $('.value_items_list', wrap).html() );

        $('.to select', list_template).attr('name', element_name+'['+field+']').data('field', field);

        if (data.field_value !== false) {
            $('.to select', list_template).val(data.field_value);
        }

        $('.list_wrap', wrap).append($(list_template).show().data('field', field));

        return cancelValue();

    }

    $('.add_link', wrap).on('click', function (){
        return addValue();
    });

    $('.add_list .cancel', wrap).on('click', function (){
        return cancelValue();
    });

    $('.add_list .add_value', wrap).on('click', function (){
        return submitValue();
    });

    $('.unset_value', wrap).on('click', function (){
        return unsetValue(this);
    });

    for(var field in current_values) {
        if(current_values.hasOwnProperty(field)){
            submitValue({field: field, field_value: current_values[field]});
        }
    }

};
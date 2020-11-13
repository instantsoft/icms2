var icms = icms || {};
icms.dynamicList = function(field_id, element_name, current_values, fields_mapping, single_select){

    fields_mapping = fields_mapping || {};
    single_select = single_select || 0;

    var mapping_data = {};

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
        $('.key_items_list option[id=key_option_'+field_id+'_'+$(wrap_value).data('field')+']', wrap).prop('disabled', false);
        $(wrap_value).remove();
        cancelValue();
        return false;
    };

    var buildData = function (data){

        if(data && Object.keys(fields_mapping).length > 0){
            for(var name in data) {
                if(fields_mapping.hasOwnProperty(name)){
                    mapping_data[fields_mapping[name]] = data[name];
                }
            }
        } else {
            mapping_data = data || {};
            if(typeof (mapping_data) !== 'object'){
                mapping_data = {field: '', field_select: mapping_data};
            }
        }

        var default_data = {field: false, field_select: false, field_value: false};

        return $.extend(default_data, mapping_data);

    };

    var submitValue = function (data){

        data = buildData(data);

        if (data.field !== false){
            var field = data.field;
        } else {
            var field = $('.add_list select', wrap).val();
            data.field = field || false;
        }

        var list_template = $('.list_template', wrap).clone(true).removeClass('list_template').addClass('list_fields_list');

        var field_title = $('.key_items_list option[value="'+field+'"]', wrap).html();
        if(single_select > 0){
            $('.key_items_list option[id=key_option_'+field_id+'_'+field+']', wrap).prop('disabled', true);
        }

        var ns = $('.key_items_list option[value="'+field+'"]', wrap).data('ns');

        $('.title', list_template).append(field_title);
        $('.to select', list_template).html( $('.value_items_list'+(ns ? '.'+ns : ''), wrap).html() );

        if(Object.keys(fields_mapping).length > 0){

            var reversed_fields_mapping = {};
            for (var key in fields_mapping) {
                reversed_fields_mapping[fields_mapping[key]] = key;
            }

            var element_id = $('.list_wrap > div', wrap).length;

            $('.title input', list_template).attr('name', element_name+'['+element_id+']['+reversed_fields_mapping['field']+']');

            if(reversed_fields_mapping.hasOwnProperty('field_select')){

                $('.to select', list_template).attr('name', element_name+'['+element_id+']['+reversed_fields_mapping['field_select']+']').data('field', field);

            } else {
                $('.to select', list_template).remove();
            }

            if(reversed_fields_mapping.hasOwnProperty('field_value')){

                $('.value input', list_template).show().attr('name', element_name+'['+element_id+']['+reversed_fields_mapping['field_value']+']');

                if (data.field_value !== false) {
                    $('.value input', list_template).val(data.field_value);
                }

            } else {
                $('.value input', list_template).remove();
            }

            if (data.field !== false) {
                $('.title input', list_template).val(data.field);
            }

        } else {

            $('.to select', list_template).attr('name', element_name+'['+field+']').data('field', field);

            $('.value input', list_template).remove();
            $('.title input', list_template).remove();

        }

        if (data.field_select !== false) {
            $('.to select', list_template).val(data.field_select);
        }

        $('.list_wrap', wrap).append($(list_template).show().data('field', field));

        return cancelValue();

    };

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
            if($.isNumeric(field)){
                submitValue(current_values[field]);
            } else {
                submitValue({field: field, field_select: current_values[field]});
            }
        }
    }

};
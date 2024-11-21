var icms = icms || {};
icms.dynamicList = function(field_id, element_name, current_values, fields_mapping, single_select){

    fields_mapping = fields_mapping || {};
    single_select = single_select || 0;

    let element_key = 0;

    let mapping_data = {};

    let wrap = $('#list_wrap_'+field_id);

    let addValue = function (){
        $('.add_list select', wrap).html($('.key_items_list', wrap).html()).show();
        $('.add_list', wrap).show();
        $('.add_link', wrap).hide();
        return false;
    };

    let cancelValue = function (){
        $('.add_list', wrap).hide();
        $('.add_link', wrap).show();
        return false;
    };

    let unsetValue = function (link){
        let wrap_value = $(link).parent('span').parent('div');
        $('.key_items_list option[id=key_option_'+field_id+'_'+$(wrap_value).data('field')+']', wrap).prop('disabled', false);
        $(wrap_value).remove();
        cancelValue();
        return false;
    };

    let buildData = function (data){

        if(data && Object.keys(fields_mapping).length > 0){
            for(let name in data) {
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

        let default_data = {field: false, field_select: false, field_value: false};

        return $.extend(default_data, mapping_data);
    };

    let submitValue = function (data){

        element_key += 1;

        data = buildData(data);

        let field;

        if (data.field !== false){
            field = data.field;
        } else {
            field = $('.add_list select', wrap).val();
            data.field = field || false;
        }

        let list_template = $('.list_template', wrap).clone(true).removeClass('list_template').addClass('list_fields_list mb-3');

        let field_title = $('.key_items_list option[value="'+field+'"]', wrap).html();
        if(single_select > 0){
            $('.key_items_list option[id=key_option_'+field_id+'_'+field+']', wrap).prop('disabled', true);
        }

        let ns = $('.key_items_list option[value="'+field+'"]', wrap).data('ns');

        $('.title', list_template).append(field_title);
        $('.to select', list_template).html( $('.value_items_list'+(ns ? '.'+ns : ''), wrap).html() );

        if(Object.keys(fields_mapping).length > 0){

            let reversed_fields_mapping = {};
            for (let key in fields_mapping) {
                reversed_fields_mapping[fields_mapping[key]] = key;
            }

            $('.title input', list_template).attr('name', element_name+'['+element_key+']['+reversed_fields_mapping['field']+']');

            if(reversed_fields_mapping.hasOwnProperty('field_select')){

                $('.to select', list_template).attr('name', element_name+'['+element_key+']['+reversed_fields_mapping['field_select']+']').data('field', field);

            } else {
                $('.to select', list_template).remove();
            }

            if(reversed_fields_mapping.hasOwnProperty('field_value')){

                $('.value input', list_template).show().attr('name', element_name+'['+element_key+']['+reversed_fields_mapping['field_value']+']');

                if (data.field_value !== false) {
                    $('.value input', list_template).val(data.field_value);
                }
                let placeholder = $('.key_items_list option[value="'+field+'"]', wrap).data('placeholder');
                if(placeholder){
                    $('.value input', list_template).attr('placeholder', placeholder);
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

    for(let field in current_values) {
        if(current_values.hasOwnProperty(field)){
            if($.isNumeric(field)){
                submitValue(current_values[field]);
            } else {
                submitValue({field: field, field_select: current_values[field]});
            }
        }
    }
};
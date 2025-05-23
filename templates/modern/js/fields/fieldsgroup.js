var icms = icms || {};
icms.fieldsgroup = function(field_id, element_name, current_values, errors, is_dynamic){

    var wrap = $('#fieldsgroup_'+field_id);

    var unsetValue = function (link){
        $(link).closest('.fieldsgroup-item').remove();
        return false;
    };

    var submitValue = function (data, err){

        let list_template = $($('.fieldsgroup-template', wrap).html());

        if (is_dynamic) {
            list_template.find('label').removeAttr('for');
            list_template.find('input, select, textarea').removeAttr('id');
        }

        for (let name in data) {

            let field = $("[name*='["+name+"]']", list_template).not('.disable_js');
            let field_wrap = field.closest('.form-field__child');

            if (field.attr('type') === 'checkbox') {
                field.prop('checked', data[name] ? true : false);
            } else {
                field.val(data[name]);
            }

            if(err[name]){
                field_wrap.addClass('field_error').find('.invalid-feedback').text(err[name]);
            }
        }

        $('.fieldsgroup_wrap', wrap).append(list_template);

        return false;
    };

    $(wrap).on('click', '.delete', function (){
        return unsetValue(this);
    });

    $('.add_link', wrap).on('click', function (){
        return submitValue();
    });

    if (is_dynamic) {
        if(current_values){
            for(let order_key in current_values) {
                if(current_values.hasOwnProperty(order_key)){
                    submitValue(current_values[order_key], (errors[order_key] ? errors[order_key] : {}));
                }
            }
        }
    } else {
        submitValue((current_values ? current_values : {}), (errors ? errors : {}));
    }
};
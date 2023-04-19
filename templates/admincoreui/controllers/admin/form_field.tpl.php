<?php
    $form_id = md5(microtime(true));
    $this->renderForm($form, $field, [
        'action' => '',
        'form_id' => $form_id,
        'method' => 'post'
    ], $errors);
?>

<script>

    var filter_options_el = $('#fset_visibility > .field').length === 1 ? '#tab-visibility' : '#f_is_in_filter, #tab-filter_access';
    var virtual_options_el = '#tab-format, #tab-profile, #tab-add_access, #tab-edit_access, #tab-author_access';

    function loadFieldTypeOptions(field){

        $('#tab-field_settings').remove();

        var field_type = $(field).val();

        if(field_type){
            $.post('<?php echo $fields_options_link; ?>', {
                <?php if (!empty($field['id'])) { ?>
                    field_id: '<?php echo $field['id']; ?>',
                <?php } ?>
                form_id: '<?php echo $form_id; ?>',
                ctype_name: '<?php echo isset($ctype_name) ? $ctype_name : ''; ?>',
                type: field_type
            }, function(data){

                if (!data) { return; }

                if(data.error){
                    icms.modal.alert(data.message, 'ui_error'); return;
                }

                if(!data.html){
                    return;
                }

                if(!data.is_can_in_filter){
                    $(filter_options_el).hide(); $('#is_in_filter').prop('checked', false);
                } else {
                    $(filter_options_el).show();
                }

                if(data.is_virtual){
                    $(virtual_options_el).hide();
                } else {
                    $(virtual_options_el).show();
                }

                $('#tab-type').after($(data.html));

                icms.events.run('loadfieldtypeoptions', data.html);

            }, 'json');

        }

    }

    $(function(){
        var select_type = $('#type');
        if($(select_type).attr('type') == 'hidden'){
            $('#tab-type').hide();
        }
        $(select_type).on('change', function(){
            loadFieldTypeOptions(this);
        });
        if ($('#tab-field_settings').length == 0){
            loadFieldTypeOptions(select_type);
        }
        $('#is_in_filter').on('click', function(){
            if($(this).is(':checked')){
                $('#f_options_is_autolink').fadeTo(400, 1);
                $('#options_is_autolink').prop('disabled', false);
            } else {
                $('#f_options_is_autolink').fadeTo(100, 0.5);
                $('#options_is_autolink').prop('disabled', true);
            }
        });
        icms.events.on('loadfieldtypeoptions', function(){
            $('#is_in_filter').triggerHandler('click');
        });
    });

</script>
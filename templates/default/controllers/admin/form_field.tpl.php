<?php
    $this->renderForm($form, $field, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>

<script type="text/javascript">

    function loadFieldTypeOptions(field){

        $('#fset_type > div[id!=f_type]').remove();

        var field_type = $(field).val();

        if(field_type){
            $.post('<?php echo $fields_options_link; ?>', {
                <?php if ($do=='edit') { ?>
                    field_id: '<?php echo $field['id']; ?>',
                <?php } ?>
                ctype_name: '<?php echo isset($ctype_name) ? $ctype_name : ''; ?>',
                type: field_type
            }, function( html ){
                if (!html) { return; }
                $('#f_type').after( html );
                icms.events.run('loadfieldtypeoptions', html);
            }, 'html');
        }

    }

    $(function(){
        var select_type = $('#type');
        if($(select_type).val() == 'caption'){
            $('#fset_type').hide();
        }
        $(select_type).on('change', function(){
            loadFieldTypeOptions(this);
        });
        if ($('#fset_type > div[id!=f_type]').length == 0){
            loadFieldTypeOptions(select_type);
        }
        $('#is_in_filter').on('click', function(){
            if($(this).is(':checked')){
                $('#f_is_autolink').fadeTo(400, 1);
                $('#is_autolink').prop('disabled', false);
            } else {
                $('#f_is_autolink').fadeTo(100, 0.5);
                $('#is_autolink').prop('disabled', true);
            }
        });
        $('#is_in_list').on('click', function(){
            if($(this).is(':checked')){
                $('#f_options_context_list').show().addClass('parent_field');
            } else {
                $('#f_options_context_list').hide().removeClass('parent_field');
            }
        }).triggerHandler('click');
        icms.events.on('loadfieldtypeoptions', function(){
            $('#is_in_filter').triggerHandler('click');
        });
    });

</script>
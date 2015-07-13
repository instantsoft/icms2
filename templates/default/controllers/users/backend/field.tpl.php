<?php

    $this->addBreadcrumb(LANG_USERS_CFG_FIELDS, $this->href_to('fields'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($field['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('fields')
    ));

?>

<?php
    $this->renderForm($form, $field, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>

<script type="text/javascript">

    function loadFieldTypeOptions(){

        var field_type = $('select#type').val();

        $('#fset_type div[id!=f_type]').remove();

        $.post('<?php echo $this->href_to('fields_options'); ?>', {
            <?php if ($do=='edit') { ?>
                field_id: '<?php echo $field['id']; ?>',
            <?php } ?>
            type: field_type
        }, function( html ){
            if (!html) { return; }
            $('#f_type').after( html );
        }, "html")

    }

    $(document).ready(function(){
        $('select#type').change(function(){ loadFieldTypeOptions(); });
        if ($('#fset_type div[id!=f_type]').length == 0){
            loadFieldTypeOptions();
        }
    });

</script>
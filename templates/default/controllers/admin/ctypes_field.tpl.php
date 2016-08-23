<?php if ($do=='add') { ?><h1><?php echo LANG_CP_FIELD_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_FIELD; ?>: <span><?php echo $field['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_FIELD_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_FIELD . ': ' . $field['title']); }

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    if ($do=='add'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('ctypes', array('fields', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('ctypes', array('fields', $ctype['id'])));
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
        'href'  => $this->href_to('ctypes', array('fields', $ctype['id']))
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_FIELD
	));

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
            $.post('<?php echo $this->href_to('ctypes', array('fields_options')); ?>', {
                <?php if ($do=='edit') { ?>
                    field_id: '<?php echo $field['id']; ?>',
                <?php } ?>
                ctype_name: '<?php echo $ctype['name']; ?>',
                type: field_type
            }, function( html ){
                if (!html) { return; }
                $('#f_type').after( html );
                icms.events.run('loadfieldtypeoptions', html);
            }, 'html');
        }

    }

    $(function(){
        var select_type = $('select#type');
        $(select_type).on('change', function(){
            loadFieldTypeOptions(this);
        });
        if ($('#fset_type > div[id!=f_type]').length == 0){
            loadFieldTypeOptions(select_type);
        }
    });

</script>

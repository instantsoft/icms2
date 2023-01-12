<?php if ($do=='add') { ?><h1><?php echo LANG_CP_FIELD_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_FIELD; ?>: <span><?php echo $prop['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_FIELD_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_FIELD . ': ' . $prop['title']); }

    if ($do=='add'){

        $this->addBreadcrumb(LANG_CP_CTYPE_PROPS, $this->href_to('ctypes', array('props', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do=='edit'){

        $this->addBreadcrumb(LANG_CP_CTYPE_PROPS, $this->href_to('ctypes', array('props', $ctype['id'])));
        $this->addBreadcrumb($prop['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes', array('props', $ctype['id']))
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_PROP
	));

?>

<?php
    $this->renderForm($form, $prop, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>

<script>

    $(document).ready(function(){

        $('#tab-type #type').change(function(){
            if ($(this).val()=='list' || $(this).val()=='list_multiple'){
                $('#tab-values').show();
            } else {
                $('#tab-values').hide();
            }
            if ($(this).val()=='list_multiple'){
                $('#f_options_is_filter_multi').hide();
            } else {
                $('#f_options_is_filter_multi').show();
            }
            if ($(this).val()=='number'){
                $('#tab-number').show();
            } else {
                $('#tab-number').hide();
            }
            if ($(this).val()=='color'){
                $('#f_is_in_filter').hide();
            } else {
                $('#f_is_in_filter').show();
            }
        });

        $('#tab-type #type').trigger('change');

    });

</script>
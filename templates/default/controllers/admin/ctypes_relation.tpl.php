<?php if ($do=='add') { ?><h1><?php echo LANG_CP_RELATION_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_RELATION; ?>: <span><?php echo $relation['title']; ?></span></h1><?php } ?>
<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_RELATION_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_RELATION . ': ' . $relation['title']); }

    if ($do=='add'){

        $this->addBreadcrumb(LANG_CP_CTYPE_RELATIONS, $this->href_to('ctypes', array('relations', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_RELATION_ADD);
    }

    if ($do=='edit'){

        $this->addBreadcrumb(LANG_CP_CTYPE_RELATIONS, $this->href_to('ctypes', array('relations', $ctype['id'])));
        $this->addBreadcrumb($relation['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes', array('relations', $ctype['id']))
    ));
    $this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_RELATIONS
	));

?>

<?php $this->renderForm($form, $relation, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>
<script>
    $(document).ready(function(){

        var isTitleTyped = $('input#title').val() != '';

        $('input#title').on('input', function(){
            isTitleTyped = true;
        });

        $('select#child_ctype_id').on('change', function(){
           if (!isTitleTyped){
               $('input#title').val($(this).find('option:selected').text().replace(/(.*): /gi, ''));
           }
        }).triggerHandler('change');

        $('select#layout').on('change', function(){
           $('form #tab-tab-opts fieldset').toggle( $(this).val() == 'tab' );
        }).triggerHandler('change');

    });
</script>
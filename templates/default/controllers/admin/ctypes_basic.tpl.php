<?php if ($do=='add') { ?><h1><?php echo LANG_CP_CTYPES_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_CTYPES_ADD); }
    if ($do=='edit') { $this->setPageTitle(LANG_CONTENT_TYPE . ': ' . $ctype['title']); }

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_CTYPES_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($ctype['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ));
    $this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_BASIC
	));
?>

<div class="pills-menu">
    <?php $this->menu('admin_toolbar'); ?>
</div>

<?php
    $this->renderForm($form, $ctype, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>

<script>
    $(function(){
        $('#f_name .input').on('input', function(){
            $('#f_url_pattern .prefix').html('/'+$(this).val()+'/');
        });
    });
</script>
<h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1>

<?php

    $this->setPageTitle(LANG_CP_CTYPE_PERMISSIONS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_PERMISSIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => null,
        'onclick' => "icms.forms.submit()"
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
		'href'  => LANG_HELP_URL_CTYPES_PERMS
	));

?>

<div class="pills-menu">
    <?php $this->menu('admin_toolbar'); ?>
</div>

<?php

    $submit_url = $this->href_to('ctypes', array('perms_save', $ctype['name']));

    echo $this->renderPermissionsGrid($rules, $groups, $values, $submit_url);

?>

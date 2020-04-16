<?php if ($do=='add') { ?><h1><?php echo LANG_CP_USER_GROUP_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_USER_GROUP; ?>: <span><?php echo $group['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_USER_GROUP_ADD); }
    if ($do=='edit') { $this->setPageTitle(LANG_USER_GROUP.': '.$group['title']); }

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
        $this->addBreadcrumb(LANG_CP_USER_GROUP_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
        $this->addBreadcrumb($group['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('users')
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_USERS_GROUP
	));

    $this->setMenuItems('users_group', $menu);

?>

<div class="pills-menu">
    <?php $this->menu('users_group'); ?>
</div>

<?php
    $this->renderForm($form, $group, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>

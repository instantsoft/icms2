<h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1>

<?php

    $this->setPageTitle(LANG_CP_CTYPE_LABELS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_LABELS);

    $this->addMenuItems('ctype', $this->controller->getCtypeMenu('edit', $id));

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
		'class'  => 'help',
		'title'  => LANG_HELP,
		'target' => '_blank',
		'href'   => LANG_HELP_URL_CTYPES_LABELS
	));

?>

<div class="pills-menu">
    <?php $this->menu('ctype'); ?>
</div>

<?php
    $this->renderForm($form, $ctype, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

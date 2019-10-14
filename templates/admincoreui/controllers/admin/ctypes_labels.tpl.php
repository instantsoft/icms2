<?php

    $this->setPageTitle(LANG_CP_CTYPE_LABELS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_LABELS);

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('edit', $ctype['id']));

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

    $this->renderForm($form, $ctype, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

<?php

    $this->setPageTitle(LANG_CP_CTYPE_DATASETS);

    $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_DATASET_ADD,
        'href'  => href_to('admin', 'ctypes', array('datasets_add', 'groups'))
    ));
    $this->addToolButton(array(
        'class'   => 'save',
        'title'   => LANG_SAVE_ORDER,
        'href'    => null,
        'onclick' => "icms.datagrid.submit('".href_to('admin', 'ctypes', array('datasets_reorder', 'groups'))."')"
    ));
	$this->addToolButton(array(
		'class'  => 'help',
		'title'  => LANG_HELP,
		'target' => '_blank',
		'href'   => LANG_HELP_URL_CTYPES_DATASETS
	));

    $this->renderGrid($this->href_to('datasets'), $grid);

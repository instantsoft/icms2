<?php

    $this->setPageTitle(LANG_CP_CTYPE_DATASETS);

    $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_DATASET_ADD,
        'href'  => $this->href_to('datasets', 'add')
    ));

	$this->addToolButton(array(
		'class'  => 'help',
		'title'  => LANG_HELP,
		'target' => '_blank',
		'href'   => LANG_HELP_URL_CTYPES_DATASETS
	));

    $this->renderGrid($this->href_to('datasets'), $grid);

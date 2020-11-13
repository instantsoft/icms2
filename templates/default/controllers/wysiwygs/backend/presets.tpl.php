<?php

    $this->addBreadcrumb(LANG_WW_PRESETS);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_ADD,
        'href'  => $this->href_to('presets_add')
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_WYSIWYGS
	));

?>

<?php $this->renderGrid($this->href_to('presets'), $grid);

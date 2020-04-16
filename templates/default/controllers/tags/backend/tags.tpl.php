<?php
    $this->addToolButton(array(
        'class' => 'refresh',
        'title' => LANG_TAGS_RECOUNT,
        'href'  => $this->href_to('recount')
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_TAGS
	));
?>

<?php $this->renderGrid($this->href_to('ajax'), $grid); ?>

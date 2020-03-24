<?php
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_RSS
	));
?>

<?php $this->renderGrid($this->href_to(''), $grid);

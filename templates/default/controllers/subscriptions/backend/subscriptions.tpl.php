<?php

    $this->setPageTitle(LANG_SBSCR_LIST);
    $this->addBreadcrumb(LANG_SBSCR_LIST);

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_SUBSCRIPTIONS
	));

    $this->addToolButton(array(
        'class' => 'delete show_on_selected',
        'title' => LANG_DELETE,
        'href'  => null,
        'onclick' => "return icms.datagrid.submit('".$this->href_to('delete').'?csrf_token='.cmsForm::getCSRFToken()."', '".LANG_DELETE_SELECTED_CONFIRM."')",
    ));

    $this->renderGrid($this->href_to('list'), $grid);

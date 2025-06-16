<?php
    $this->addBreadcrumb(LANG_BILLING_CP_ADD_BAL);
	$this->setPageTitle(LANG_BILLING_CP_ADD_BAL);

    $this->renderForm($form, $options, [
        'action' => '',
        'method' => 'post',
		'submit' => [
			'title' => LANG_CONTINUE
		]
    ], $errors);

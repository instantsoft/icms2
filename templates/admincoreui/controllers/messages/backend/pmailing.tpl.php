<?php

    $this->setPageTitle(LANG_PM_PMAILING);
    $this->addBreadcrumb(LANG_PM_PMAILING);

    $this->renderForm($form, $mailing, [
        'action' => '',
        'method' => 'post',
        'submit' => [
            'title' => LANG_SUBMIT
        ]
    ], $errors);

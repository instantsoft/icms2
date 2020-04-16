<?php

    $this->setPageTitle(LANG_CP_SETTINGS_TEMPLATE_OPTIONS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SETTINGS_TEMPLATE_OPTIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('settings')
    ));

    $this->renderForm($form, $options, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

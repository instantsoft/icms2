<?php

    if ($do=='add') { $this->setPageTitle(LANG_FORMS_CP_FORMS_ADD); }
    if ($do=='edit') { $this->setPageTitle(LANG_FORMS_CP_FORMS_EDIT . ': ' . $form_data['title']); }

    $this->setMenuItems('backend', $menu);

    if ($do=='add'){
        $this->addBreadcrumb(LANG_FORMS_CP_FORMS_ADD);
    }

    if ($do=='copy'){
        $this->addBreadcrumb(LANG_FORMS_CP_FORMS_COPY);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($form_data['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('')
    ));

    $this->renderForm($form, $form_data, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

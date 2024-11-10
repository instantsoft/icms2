<?php

    if ($do=='add') { $this->setPageTitle(LANG_FORMS_CP_FORMS_ADD); }
    if ($do=='edit') { $this->setPageTitle(LANG_FORMS_CP_FORMS_EDIT . ': ' . $form_data['title']); }

    $this->addMenuItems('admin_toolbar', $menu);

    if ($do=='add'){
        $this->addBreadcrumb(LANG_FORMS_CP_FORMS_ADD);
    }

    if ($do=='copy'){
        $this->addBreadcrumb(LANG_FORMS_CP_FORMS_COPY);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($form_data['title']);
    }

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to(''),
        'icon'  => 'undo'
    ]);

    $this->renderForm($form, $form_data, [
        'action' => '',
        'method' => 'post'
    ], $errors);

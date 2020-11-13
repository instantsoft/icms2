<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_FIELD_ADD, $form_data['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_FIELD . ': ' . $form_data['title']); }

    $this->setMenuItems('backend', $menu);

    $this->addBreadcrumb($form_data['title'], $this->href_to('edit', array($form_data['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('form_fields', array($form_data['id'])));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($field['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('form_fields', array($form_data['id']))
    ));

    $this->renderControllerChild('admin', 'form_field', array(
        'fields_options_link' => $this->href_to('fields_options'),
        'ctype_name'          => 'forms',
        'do'                  => $do,
        'errors'              => $errors,
        'form'                => $form,
        'field'               => $field
    ));

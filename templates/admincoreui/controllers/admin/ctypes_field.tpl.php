<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_FIELD_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_FIELD . ': ' . $field['title']); }

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('datasets', $ctype['id']));

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    if ($do=='add'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('ctypes', array('fields', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('ctypes', array('fields', $ctype['id'])));
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
        'href'  => $this->href_to('ctypes', array('fields', $ctype['id']))
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FIELD,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->renderControllerChild('admin', 'form_field', array(
        'fields_options_link' => $this->href_to('ctypes', array('fields_options')),
        'ctype_name'          => $ctype['name'],
        'do'                  => $do,
        'errors'              => $errors,
        'form'                => $form,
        'field'               => $field
    ));

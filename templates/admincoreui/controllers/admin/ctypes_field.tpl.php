<?php

    if ($do === 'add') { $this->setPageTitle(LANG_CP_FIELD_ADD, $ctype['title']); }
    if ($do === 'edit') { $this->setPageTitle(LANG_CP_FIELD . ': ' . $field['title']); }

    $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('ctypes', ['fields', $ctype['id']]));

    if ($do === 'add'){

        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do === 'edit'){

        $this->addBreadcrumb($field['title']);
    }

    $this->addToolButton([
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => 'javascript:icms.forms.submit()'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes', ['fields', $ctype['id']])
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FIELD,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderControllerChild('admin', 'form_field', [
        'fields_options_link' => $this->href_to('ctypes', ['fields_options']),
        'ctype_name'          => $ctype['name'],
        'do'                  => $do,
        'errors'              => $errors,
        'form'                => $form,
        'field'               => $field
    ]);

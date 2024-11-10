<?php
    $this->addTplJSName([
        'admin-prop'
    ]);

    if ($do === 'add') { $this->setPageTitle(LANG_CP_FIELD_ADD, $ctype['title']); }
    if ($do === 'edit') { $this->setPageTitle(LANG_CP_FIELD . ': ' . $prop['title']); }

    $this->addBreadcrumb(LANG_CP_CTYPE_PROPS, $this->href_to('ctypes', ['props', $ctype['id']]));

    if ($do === 'add'){
        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do === 'edit'){
        $this->addBreadcrumb($prop['title']);
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
        'href'  => $this->href_to('ctypes', ['props', $ctype['id']]),
        'icon'  => 'undo'
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_PROP,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderControllerChild('admin', 'form_field', [
        'fields_options_link' => $this->href_to('ctypes', ['fields_options']),
        'ctype_name'          => $ctype['name'],
        'postfix'             => '_props',
        'do'                  => $do,
        'errors'              => $errors,
        'form'                => $form,
        'field'               => $prop
    ]);

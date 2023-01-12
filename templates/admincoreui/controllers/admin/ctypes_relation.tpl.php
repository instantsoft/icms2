<?php

    $this->addTplJSName('admin-relation');

    if ($do === 'add') { $this->setPageTitle(LANG_CP_RELATION_ADD, $ctype['title']); }
    if ($do === 'edit') { $this->setPageTitle(LANG_CP_RELATION . ': ' . $relation['title']); }

    $this->addBreadcrumb(LANG_CP_CTYPE_RELATIONS, $this->href_to('ctypes', ['relations', $ctype['id']]));

    if ($do === 'add'){
        $this->addBreadcrumb(LANG_CP_RELATION_ADD);
    }

    if ($do === 'edit'){
        $this->addBreadcrumb($relation['title']);
    }

    $this->addToolButton([
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => 'javascript:icms.forms.submit()'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes', ['relations', $ctype['id']])
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_RELATIONS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderForm($form, $relation, [
        'action' => '',
        'method' => 'post'
    ], $errors);

<?php

    $this->setPageTitle(LANG_CP_CTYPE_FIELDS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS);

    $this->addToolButton([
        'class' => 'add',
        'title' => LANG_CP_FIELD_ADD,
        'href'  => $this->href_to('ctypes', ['fields_add', $ctype['id']])
    ]);

    $this->addToolButton([
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FIELDS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderGrid($this->href_to('ctypes', ['fields', $ctype['id']]), $grid);

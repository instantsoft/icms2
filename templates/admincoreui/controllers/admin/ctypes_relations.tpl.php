<?php

    $this->setPageTitle(LANG_CP_CTYPE_RELATIONS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_RELATIONS);

    $this->addToolButton([
        'class' => 'add',
        'title' => LANG_CP_RELATION_ADD,
        'href'  => $this->href_to('ctypes', ['relations_add', $ctype['id']])
    ]);

    $this->addToolButton([
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_RELATIONS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderGrid($this->href_to('ctypes', ['relations', $ctype['id']]), $grid);

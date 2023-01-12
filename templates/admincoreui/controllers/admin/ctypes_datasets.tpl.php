<?php

    $this->setPageTitle(LANG_CP_CTYPE_DATASETS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS);

    $this->addToolButton([
        'class' => 'add',
        'title' => LANG_CP_DATASET_ADD,
        'href'  => $this->href_to('ctypes', ['datasets_add', $ctype['id']])
    ]);

    $this->addToolButton([
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_DATASETS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderGrid($this->href_to('ctypes', ['datasets', $ctype['id']]), $grid);

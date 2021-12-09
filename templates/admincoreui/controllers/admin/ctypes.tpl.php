<?php

    $this->setPageTitle(LANG_CP_SECTION_CTYPES);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_CTYPES_ADD,
        'href'  => $this->href_to('ctypes', array('add'))
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderGrid($this->href_to('ctypes', array('ajax')), $grid);

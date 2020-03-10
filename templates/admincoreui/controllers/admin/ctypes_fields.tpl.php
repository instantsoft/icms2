<?php

    $this->setPageTitle(LANG_CP_CTYPE_FIELDS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS);

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('fields', $ctype['id']));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_FIELD_ADD,
        'href'  => $this->href_to('ctypes', array('fields_add', $ctype['id']))
    ));

    $this->addToolButton(array(
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FIELDS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->renderGrid($this->href_to('ctypes', array('fields_ajax', $ctype['name'])), $grid);

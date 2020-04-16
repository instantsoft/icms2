<?php

    $this->setPageTitle(LANG_CP_CTYPE_PERMISSIONS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_PERMISSIONS);

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('perms', $ctype['id']));

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => null,
        'onclick' => "icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_PERMS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $submit_url = $this->href_to('ctypes', array('perms_save', $ctype['name']));

    echo $this->renderPermissionsGrid($rules, $groups, $values, $submit_url);

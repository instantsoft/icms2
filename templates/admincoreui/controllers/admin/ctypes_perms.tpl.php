<?php

    $this->setPageTitle(LANG_CP_CTYPE_PERMISSIONS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_PERMISSIONS);

    $this->addToolButton([
        'class'   => 'save',
        'title'   => LANG_SAVE,
        'href'    => null,
        'onclick' => 'icms.forms.submit()'
    ]);

    $this->addToolButton([
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_PERMS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $submit_url = $this->href_to('ctypes', ['perms_save', $ctype['name']]);

    echo $this->renderPermissionsGrid($rules, $groups, $values, $submit_url);

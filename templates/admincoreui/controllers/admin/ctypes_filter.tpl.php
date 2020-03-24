<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_FILTER_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_GRID_COLYMNS_FILTER . ': ' . $filter['title']); }

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    $cancel_url = $this->href_to('ctypes', array('filters', $ctype['id']));

    if ($do=='add'){

        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS, $cancel_url);
        $this->addBreadcrumb(LANG_CP_FILTER_ADD);

    }

    if ($do=='edit'){

        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS, $cancel_url);
        $this->addBreadcrumb($filter['title']);

    }

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('datasets', $ctype['id']));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FILTERS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $cancel_url
    ));

    $this->renderForm($form, $filter, array(
        'action'  => '',
        'form_id' => 'filter_form',
        'method'  => 'post'
    ), $errors);

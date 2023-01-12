<?php

    if ($do === 'add') { $this->setPageTitle(LANG_CP_FILTER_ADD, $ctype['title']); }
    if ($do === 'edit') { $this->setPageTitle(LANG_CP_GRID_COLYMNS_FILTER . ': ' . $filter['title']); }

    $cancel_url = $this->href_to('ctypes', ['filters', $ctype['id']]);

    $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS, $cancel_url);

    if ($do === 'add'){

        $this->addBreadcrumb(LANG_CP_FILTER_ADD);
    }

    if ($do === 'edit'){

        $this->addBreadcrumb($filter['title']);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FILTERS_ADD,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->addToolButton([
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => 'javascript:icms.forms.submit()'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $cancel_url
    ]);

    $this->renderForm($form, $filter, [
        'action'  => '',
        'form_id' => 'filter_form',
        'method'  => 'post'
    ], $errors);

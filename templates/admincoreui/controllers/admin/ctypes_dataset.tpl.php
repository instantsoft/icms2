<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_DATASET_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_DATASET . ': ' . $dataset['title']); }

    if($ctype['id']){
        $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('datasets', $ctype['id']));
        $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    } else {
        $this->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->href_to('controllers'));
        $this->addBreadcrumb($ctype['title'], $this->href_to('controllers', 'edit/'.$ctype['name']));
    }

    if($ctype['id']){
        $cancel_url = $this->href_to('ctypes', array('datasets', $ctype['id']));
    } else {
        $cancel_url = $this->href_to('controllers', 'edit/'.$ctype['name'].'/datasets');
    }

    if ($do=='add'){

        if($ctype['id']){
            $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        }

        $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS, $cancel_url);
        $this->addBreadcrumb(LANG_CP_DATASET_ADD);

    }

    if ($do=='edit'){

        if($ctype['id']){
            $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        }

        $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS, $cancel_url);
        $this->addBreadcrumb(LANG_CP_DATASET.': '.$dataset['title']);

    }

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

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_DATASET,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->renderForm($form, $dataset, array(
        'action'  => '',
        'form_id' => 'dataset_form',
        'method'  => 'post'
    ), $errors);

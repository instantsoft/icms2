<?php

    $this->addMenuItems('admin_toolbar', $menu);

    $this->setPageTitle(LANG_CP_CTYPE_FIELDS, $form_data['title']);

    $this->addBreadcrumb($form_data['title'], $this->href_to('edit', array($form_data['id'])));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_FIELD_ADD,
        'href'  => $this->href_to('fields_add', $form_data['id'])
    ));

    $this->renderGrid($this->href_to('form_fields', array($form_data['id'])), $grid);

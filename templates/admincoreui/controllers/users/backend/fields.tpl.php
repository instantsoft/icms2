<?php

    $this->addBreadcrumb(LANG_USERS_CFG_FIELDS);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_FIELD_ADD,
        'href'  => $this->href_to('fields_add')
    ));

    $this->renderGrid($this->href_to('fields_ajax'), $grid);

<?php

    $this->addBreadcrumb(LANG_USERS_CFG_MIGRATION);

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_USERS_MIG_ADD,
        'href'  => $this->href_to('migrations_add')
    ));

    $this->renderGrid($this->href_to('migrations_ajax'), $grid);

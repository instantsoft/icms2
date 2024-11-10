<?php

    $this->addBreadcrumb($feed['title']);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to(''),
        'icon'  => 'undo'
    ]);

    $this->renderForm($form, $feed, [
        'action' => '',
        'method' => 'post'
    ], $errors);

<?php

    $this->addBreadcrumb(LANG_IMAGES_PRESETS, $this->href_to('presets'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($preset['title']);
    }

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('presets'),
        'icon'  => 'undo'
    ]);

    $this->renderForm($form, $preset, [
        'action' => '',
        'method' => 'post'
    ], $errors);

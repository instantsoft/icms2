<?php

    $this->addBreadcrumb(LANG_IMAGES_PRESETS, $this->href_to('presets'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($preset['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('presets')
    ));

?>

<?php
    $this->renderForm($form, $preset, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

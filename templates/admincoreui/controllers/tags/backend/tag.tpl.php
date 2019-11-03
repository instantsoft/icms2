<?php

    $this->addBreadcrumb($tag['tag']);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('')
    ));

    $this->renderForm($form, $tag, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

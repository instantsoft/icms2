<?php

$title = ($do == 'edit') ? $country['name'] : LANG_GEO_ADD_COUNTRY;

$this->addBreadcrumb($title);

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

$this->renderForm($form, $country, [
    'action' => '',
    'method' => 'post',
], $errors);

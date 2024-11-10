<?php

$title = ($do == 'edit') ? $region['name'] : LANG_GEO_ADD_REGION;

$this->addBreadcrumb($country['name'], $this->href_to('regions', $country['id']));
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
    'href'  => $this->href_to('regions', $country['id']),
    'icon'  => 'undo'
]);

$this->renderForm($form, $region, [
    'action' => '',
    'method' => 'post',
], $errors);

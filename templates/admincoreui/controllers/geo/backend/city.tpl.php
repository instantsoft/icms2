<?php

$title = ($do == 'edit') ? $city['name'] : LANG_GEO_ADD_CITY;

$this->addBreadcrumb($country['name'], $this->href_to('regions', $country['id']));
$this->addBreadcrumb($region['name'], $this->href_to('cities', [$region['id'], $country['id']]));
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
    'href'  => $this->href_to('cities', [$region['id'], $country['id']]),
    'icon'  => 'undo'
]);

$this->renderForm($form, $city, [
    'action' => '',
    'method' => 'post',
], $errors);

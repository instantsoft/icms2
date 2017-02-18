<?php

$title = ($do == 'edit') ? $city['name'] : LANG_GEO_ADD_CITY;

$this->addBreadcrumb($country['name'], $this->href_to('regions', $country['id']));
$this->addBreadcrumb($region['name'], $this->href_to('cities', array($region['id'], $country['id'])));
$this->addBreadcrumb($title);

$this->addToolButton(array(
    'class' => 'save',
    'title' => LANG_SAVE,
    'href'  => 'javascript:icms.forms.submit()'
));

$this->addToolButton(array(
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $this->href_to('cities', array($region['id'], $country['id']))
));

$this->renderForm($form, $city, array(
    'action' => '',
    'method' => 'post',
), $errors);

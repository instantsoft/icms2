<?php

$title = ($do == 'edit') ? $region['name'] : LANG_GEO_ADD_REGION;

$this->addBreadcrumb($country['name'], $this->href_to('regions', $country['id']));
$this->addBreadcrumb($title);

$this->addToolButton(array(
    'class' => 'save',
    'title' => LANG_SAVE,
    'href'  => 'javascript:icms.forms.submit()'
));

$this->addToolButton(array(
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $this->href_to('regions', $country['id'])
));

$this->renderForm($form, $region, array(
    'action' => '',
    'method' => 'post',
), $errors);

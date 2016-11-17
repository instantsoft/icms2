<?php

$this->addBreadcrumb($country['name'], $this->href_to('regions', $country['id']));
$this->addBreadcrumb($region['name']);

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_GEO_ADD_CITY,
    'href'  => $this->href_to('city', array(0, $region['id']))
));

$this->renderGrid($this->href_to('cities', $region['id']), $grid);

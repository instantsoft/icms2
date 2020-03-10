<?php

$this->addBreadcrumb($country['name'], $this->href_to('regions', $country['id']));
$this->addBreadcrumb($region['name']);

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_GEO_ADD_CITY,
    'href'  => $this->href_to('city', array(0, $region['id']))
));

$this->addToolButton(array(
    'class' => 'save',
    'title' => LANG_SAVE_ORDER,
    'href'  => null,
    'onclick' => "icms.datagrid.submit('{$this->href_to('cities_reorder')}')"
));

$this->addToolButton(array(
    'class' => 'help',
    'title' => LANG_HELP,
    'target' => '_blank',
    'href'  => LANG_HELP_URL_COM_GEO,
));

$this->renderGrid($this->href_to('cities', $region['id']), $grid);

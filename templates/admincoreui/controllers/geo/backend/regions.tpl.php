<?php

$this->addBreadcrumb($country['name']);

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_GEO_ADD_REGION,
    'href'  => $this->href_to('region', $country['id'])
));

$this->renderGrid($this->href_to('regions', $country['id']), $grid);

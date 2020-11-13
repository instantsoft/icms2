<?php

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_GEO_ADD_COUNTRY,
    'href'  => $this->href_to('country')
));

$this->addToolButton(array(
    'class' => 'save',
    'title' => LANG_SAVE_ORDER,
    'href'  => null,
    'onclick' => "icms.datagrid.submit('{$this->href_to('countries_reorder')}')"
));

$this->addToolButton(array(
    'class' => 'help',
    'title' => LANG_HELP,
    'target' => '_blank',
    'href'  => LANG_HELP_URL_COM_GEO,
));

$this->renderGrid($this->href_to(''), $grid);

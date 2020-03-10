<?php

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_GEO_ADD_COUNTRY,
    'href'  => $this->href_to('country')
));

$this->renderGrid($this->href_to(''), $grid);

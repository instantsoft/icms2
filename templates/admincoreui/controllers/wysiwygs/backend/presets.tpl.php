<?php

$this->addBreadcrumb(LANG_WW_PRESETS);

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_ADD,
    'href'  => $this->href_to('presets_add')
));

$this->renderGrid($this->href_to('presets'), $grid);

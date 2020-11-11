<?php

$this->addToolButton(array(
    'class' => 'add',
    'title' => LANG_FORMS_CP_FORMS_ADD,
    'href'  => $this->href_to('add')
));

$this->renderGrid($this->href_to(''), $grid);

<?php
    $this->addToolButton(array(
        'class' => 'refresh',
        'title' => LANG_TAGS_RECOUNT,
        'href'  => $this->href_to('recount')
    ));

    $this->renderGrid($this->href_to('ajax'), $grid);

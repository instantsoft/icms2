<?php

class actionTagsIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'tags';
        $this->grid_name = 'tags';

        $this->tool_buttons = [
            [
                'class' => 'refresh',
                'title' => LANG_TAGS_RECOUNT,
                'href'  => $this->cms_template->href_to('recount')
            ]
        ];
    }

}

<?php

class actionFormsIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'forms';
        $this->grid_name  = 'forms';

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_FORMS_CP_FORMS_ADD,
                'href'  => $this->cms_template->href_to('add')
            ]
        ];
    }

}

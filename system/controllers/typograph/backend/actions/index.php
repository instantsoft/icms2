<?php

class actionTypographIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'typograph_presets';
        $this->grid_name  = 'presets';
        $this->title      = LANG_TYP_PRESETS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_ADD,
                'href'  => $this->cms_template->href_to('presets_add')
            ]
        ];
    }

}

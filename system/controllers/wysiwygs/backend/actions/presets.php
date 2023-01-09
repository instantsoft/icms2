<?php

class actionWysiwygsPresets extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'wysiwygs_presets';
        $this->grid_name  = 'presets';
        $this->title      = LANG_WW_PRESETS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_ADD,
                'href'  => $this->cms_template->href_to('presets_add')
            ]
        ];
    }

}

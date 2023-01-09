<?php

class actionImagesPresets extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'images_presets';
        $this->grid_name  = 'presets';
        $this->title      = LANG_IMAGES_PRESETS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_ADD,
                'href'  => $this->cms_template->href_to('presets_add')
            ]
        ];

        $this->list_callback = function ($model) {

            $model->orderByList([
                ['by' => 'is_internal', 'to' => 'asc'],
                ['by' => 'width', 'to' => 'asc'],
                ['by' => 'quality', 'to' => 'desc']
            ]);

            return $model;
        };
    }

}

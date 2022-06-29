<?php

class actionImagesPresets extends cmsAction {

    use icms\controllers\admin\traits\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->setProperty('table_name', 'images_presets');
        $this->setProperty('grid_name', 'presets');
        $this->setProperty('grid_url', $this->cms_template->href_to('presets'));
        $this->setProperty('title', LANG_IMAGES_PRESETS);
        $this->setProperty('tool_buttons', [
            [
                'class' => 'add',
                'title' => LANG_ADD,
                'href'  => $this->cms_template->href_to('presets_add')
            ]
        ]);
        $this->setProperty('list_callback', function ($model) {

            $model->orderByList([
                ['by' => 'is_internal', 'to' => 'asc'],
                ['by' => 'width', 'to' => 'asc'],
                ['by' => 'quality', 'to' => 'desc']
            ]);

            return $model;
        });

    }

}

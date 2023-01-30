<?php

class actionActivityTypes extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'activity_types';
        $this->grid_name  = 'types';
        $this->title      = LANG_ACTIVITY_TYPES;

        $this->model->localizedOff();
    }

}

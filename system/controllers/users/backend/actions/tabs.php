<?php

class actionUsersTabs extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = '{users}_tabs';
        $this->grid_name  = 'tabs';
        $this->title      = LANG_USERS_CFG_TABS;

        $this->list_callback = function ($model) {

            $model->orderBy('ordering');

            return $model;
        };

    }

}

<?php

class actionBillingSystems extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'billing_systems';
        $this->grid_name  = 'systems';
        $this->title      = LANG_BILLING_CP_SYSTEMS;
    }

}

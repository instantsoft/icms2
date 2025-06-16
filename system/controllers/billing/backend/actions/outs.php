<?php

class actionBillingOuts extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'billing_outs';
        $this->grid_name  = 'outs';
        $this->title      = LANG_BILLING_CP_OUT;

        $this->external_action_prefix = 'outs_';

        $this->list_callback = function (cmsModel $model) {

            $model->filterGt('status', 0);

            return $model->joinUserLeft();
        };

    }

}

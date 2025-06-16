<?php

class actionBillingLog extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'billing_log';
        $this->grid_name  = 'log';
        $this->title      = LANG_BILLING_LOG_HISTORY;

        $this->external_action_prefix = 'log_';

        $this->list_callback = function (cmsModel $model) {

            $model->joinLeft('billing_systems', 's', 's.id = i.system_id')->select('s.title', 'system_title');

            $model->filterEqual('status', 1);

            return $model->joinUserLeft();
        };

    }

}

<?php

class actionCspReports extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'csp_logs';
        $this->grid_name  = 'reports';
        $this->title  = LANG_CSP_REPORTS;

    }

}

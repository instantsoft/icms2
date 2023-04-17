<?php

class actionAdminSettingsSchedulerDelete extends cmsAction {

    use icms\traits\controllers\actions\deleteItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name  = 'scheduler_tasks';
        $this->success_url = $this->cms_template->href_to('settings', 'scheduler');

    }

}

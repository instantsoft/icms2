<?php

class actionWysiwygsPresetsDelete extends cmsAction {

    use icms\traits\controllers\actions\deleteItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name  = 'wysiwygs_presets';
        $this->success_url = $this->cms_template->href_to('presets');

    }

}

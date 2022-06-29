<?php

class actionUsersTabs extends cmsAction {

    use icms\controllers\admin\traits\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->setProperty('table_name', '{users}_tabs');
        $this->setProperty('grid_name', 'tabs');
        $this->setProperty('grid_url', $this->cms_template->href_to('tabs'));
        $this->setProperty('title', LANG_USERS_CFG_TABS);
        $this->setProperty('list_callback', function ($model) {

            $model->orderBy('ordering');

            return $model;
        });

    }

}

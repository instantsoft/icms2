<?php

class actionRssIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'rss_feeds';
        $this->grid_name  = 'feeds';
        $this->title      = LANG_RSS_CONTROLLER;

    }

}

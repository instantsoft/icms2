<?php

class actionRssIndex extends cmsAction {

    use icms\controllers\admin\traits\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->setProperty('table_name', 'rss_feeds');
        $this->setProperty('grid_name', 'feeds');
        $this->setProperty('grid_url', $this->cms_template->href_to(''));

    }

}

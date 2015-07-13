<?php

class actionRssAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('feeds');

        $rss_model = cmsCore::getModel('rss');

        $rss_model->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        if ($filter_str){
            parse_str($filter_str, $filter);
            $rss_model->applyGridFilter($grid, $filter);
        }

        $total = $rss_model->getFeedsCount();
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
        $pages = ceil($total / $perpage);

        $feeds = $rss_model->getFeeds();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $feeds, $total, $pages);

        $this->halt();

    }

}

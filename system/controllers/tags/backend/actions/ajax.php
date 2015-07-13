<?php

class actionTagsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('tags');

        $tags_model = cmsCore::getModel('tags');

        $tags_model->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        if ($filter_str){
            parse_str($filter_str, $filter);
            $tags_model->applyGridFilter($grid, $filter);
        }

        $total = $tags_model->getTagsCount();
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
        $pages = ceil($total / $perpage);

        $tags = $tags_model->getTags();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $tags, $total, $pages);

        $this->halt();

    }

}

<?php
/**
 * @property \modelContent $model
 */
class onContentFulltextSearchHtml extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($ctype_name, $filter, $page_url) {

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return '';
        }

        cmsModel::cacheResult('current_ctype', $ctype);

        list($select, $filter_str) = $filter['filter_query'];

        $this->model->filter($filter_str);

        if ($select) {
            foreach ($select as $alias => $str) {
                $this->model->select($str, $alias);
            }
        }

        foreach ($filter['filters'] as $apply_filter) {
            if ($apply_filter) {
                $this->model->filter($apply_filter);
            }
        }

        $this->model->orderByRaw($filter['order_raw']);

        return $this->setListContext('search')->renderItemsList($ctype, $page_url, false, 0, [], false, $filter['http_query']);
    }

}

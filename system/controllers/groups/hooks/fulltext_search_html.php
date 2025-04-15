<?php
/**
 * @property \modelGroups $model
 */
class onGroupsFulltextSearchHtml extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($ctype_name, $filter, $page_url) {

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

        if ($this->cms_user->is_admin) {
            $this->model->disableApprovedFilter();
        }

        return $this->renderGroupsList($page_url, false, $filter['http_query']);
    }

}

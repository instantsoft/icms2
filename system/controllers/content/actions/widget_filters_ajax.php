<?php

class actionContentWidgetFiltersAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax() || !cmsUser::isAdmin()) {
            return cmsCore::error404();
        }

        $list = ['0' => ''];

        $ctype_id = $this->request->get('value', 0);
        if (!$ctype_id) {
            return $this->cms_template->renderJSON($list);
        }

        $ctype = $this->model->getContentType($ctype_id);
        if (!$ctype) {
            return $this->cms_template->renderJSON($list);
        }

        if(!$this->model->isFiltersTableExists($ctype['name'])){
            return $this->cms_template->renderJSON($list);
        }

        $filters = $this->model->getContentFilters($ctype['name']);

        if ($filters) {
            foreach ($filters as $filter) {
                $list[] = ['title' => $filter['title'], 'value' => $filter['id']];
            }
        }

        return $this->cms_template->renderJSON($list);
    }

}

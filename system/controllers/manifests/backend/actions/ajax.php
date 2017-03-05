<?php

class actionManifestsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('manifests');

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        $filter_str = cmsUser::getUPSActual('manifests.grid_filter.events', $filter_str);

        if ($filter_str){
            parse_str($filter_str, $filter);
            $this->model->applyGridFilter($grid, $filter);
        }

        $manifests = $this->model->getManifests();

        return $this->cms_template->renderGridRowsJSON($grid, $manifests);

    }

}
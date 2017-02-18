<?php

class actionGeoGetRegionsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $country_id = $this->request->get('value', 0);
        if (!$country_id) { cmsCore::error404(); }

        $items = $this->model->getRegions($country_id);

        return $this->cms_template->renderJSON($items);

    }

}

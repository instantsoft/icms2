<?php

class actionGeoGetItems extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $type      = $this->request->get('type', '');
        $parent_id = $this->request->get('parent_id', 0);

        if (!$type || !in_array($type, ['regions', 'cities'])) {
            return cmsCore::error404();
        }

        if (!$parent_id) {
            return cmsCore::error404();
        }

        $items = [];
        $data  = [];

        switch ($type) {

            case 'regions':
                $items       = $this->model->getRegions($parent_id);
                $select_text = LANG_GEO_SELECT_REGION;
                break;

            case 'cities':
                $items       = $this->model->getCities($parent_id);
                $select_text = LANG_GEO_SELECT_CITY;
                break;
        }

        if ($items) {
            $items = ['' => $select_text] + $items;
        }

        foreach ($items as $id => $name) {
            $data[] = [
                'id'   => $id,
                'name' => $name,
            ];
        }

        return $this->cms_template->renderJSON([
            'error' => $data ? false : true,
            'items' => $data
        ]);
    }

}

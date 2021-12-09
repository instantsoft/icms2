<?php

class actionWysiwygsPresets extends cmsAction {

    public function run() {

        $grid = $this->loadDataGrid('presets');

        if ($this->request->isAjax()) {

            $filter     = [];
            $filter_str = $this->request->get('filter', '');

            $filter_str = cmsUser::getUPSActual('admin.grid_filter.wysiwyg_presets', $filter_str);

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $presets = $this->model->getPresets();

            return $this->cms_template->renderGridRowsJSON($grid, $presets);
        }

        return $this->cms_template->render('backend/presets', [
            'grid' => $grid
        ]);
    }

}

<?php

class actionAdminCtypesDatasets extends cmsAction {

    public function run($ctype_id = null) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $this->model_content->localizedOn();

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->dispatchEvent('ctype_loaded', [$ctype, 'datasets']);

        $grid = $this->loadDataGrid('ctype_datasets', [href_to('admin', 'ctypes', ['datasets_reorder', $ctype['id']]), href_to($this->name, 'ctypes', ['datasets_edit', '{id}'])]);

        if ($this->request->isAjax()) {

            $this->model_content->orderBy('ordering', 'asc');

            $datasets = $this->model_content->getContentDatasets($ctype_id);

            $this->cms_template->renderGridRowsJSON($grid, $datasets);

            return $this->halt();
        }

        $this->model_content->localizedOff();

        return $this->cms_template->render('ctypes_datasets', [
            'ctype' => $ctype,
            'grid'  => $grid
        ]);
    }

}

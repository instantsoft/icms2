<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesFilters extends cmsAction {

    public function run($ctype_id = null) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->dispatchEvent('ctype_loaded', [$ctype, 'filters']);

        $table_exists = $this->model_backend_content->isFiltersTableExists($ctype['name']);

        if (!$table_exists) {

            return $this->cms_template->render('ctypes_filters', [
                'table_exists' => $table_exists,
                'ctype'        => $ctype,
                'grid'         => []
            ]);
        }

        $grid = $this->loadDataGrid('ctype_filters', ['ctype' => $ctype]);

        if ($this->request->isAjax()) {

            $this->model_backend_content->orderBy('id', 'asc');

            $filters = $this->model_backend_content->getContentFilters($ctype['name']);

            $this->cms_template->renderGridRowsJSON($grid, $filters);

            return $this->halt();
        }

        return $this->cms_template->render('ctypes_filters', [
            'table_exists' => $table_exists,
            'ctype'        => $ctype,
            'grid'         => $grid
        ]);
    }

}

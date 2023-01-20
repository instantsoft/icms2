<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesFields extends cmsAction {

    public function run($ctype_id = null) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->localizedOn()->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->dispatchEvent('ctype_loaded', [$ctype, 'fields']);

        $grid = $this->loadDataGrid('ctype_fields', $ctype['name']);

        if ($this->request->isAjax()) {

            $filter     = [];
            $filter_str = cmsUser::getUPSActual('admin.grid_filter.ctypes_fields', $this->request->get('filter', ''));

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model_backend_content->applyGridFilter($grid, $filter);
            }

            $this->model_backend_content->orderBy('ordering', 'asc');

            $fields = $this->model_backend_content->getContentFields($ctype['name'], false, false);

            $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

            $this->cms_template->renderGridRowsJSON($grid, $fields);

            return $this->halt();
        }

        return $this->cms_template->render('ctypes_fields', [
            'ctype' => $ctype,
            'grid'  => $grid
        ]);
    }

}

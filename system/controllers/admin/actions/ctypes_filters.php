<?php

class actionAdminCtypesFilters extends cmsAction {

    public function run($ctype_id = null){

        if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $table_exists = $this->model_content->isFiltersTableExists($ctype['name']);

        if(!$table_exists){
            return $this->cms_template->render('ctypes_filters', array(
                'table_exists' => $table_exists,
                'ctype' => $ctype,
                'grid'  => []
            ));
        }

        $grid = $this->loadDataGrid('ctype_filters', ['ctype' => $ctype]);

        if ($this->request->isAjax()) {

            $this->model_content->orderBy('id', 'asc');

            $filters = $this->model_content->getContentFilters($ctype['name']);

            $this->cms_template->renderGridRowsJSON($grid, $filters);

            $this->halt();

        }

        return $this->cms_template->render('ctypes_filters', array(
            'table_exists' => $table_exists,
            'ctype' => $ctype,
            'grid'  => $grid
        ));

    }

}

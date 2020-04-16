<?php

class actionAdminCtypesRelations extends cmsAction {

    public function run($ctype_id = null){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_relations', href_to('admin', 'ctypes', ['relations_reorder', $ctype['id']]));

        if ($this->request->isAjax()) {

            $relations = $content_model->getContentRelations($ctype_id);

            $this->cms_template->renderGridRowsJSON($grid, $relations);

            $this->halt();

        }

        return $this->cms_template->render('ctypes_relations', array(
            'ctype' => $ctype,
            'grid'  => $grid
        ));

    }

}

<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesRelations extends cmsAction {

    public function run($ctype_id = null) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->localizedOn()->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $grid = $this->loadDataGrid('ctype_relations', href_to('admin', 'ctypes', ['relations_reorder', $ctype['id']]));

        if ($this->request->isAjax()) {

            $relations = $this->model_backend_content->getContentRelations($ctype_id);

            $this->cms_template->renderGridRowsJSON($grid, $relations);

            return $this->halt();
        }

        // Для того, чтобы сформировалось подменю типа контента, см system/controllers/admin/actions/ctypes.php
        $this->dispatchEvent('ctype_loaded', [$ctype, 'relations']);

        return $this->cms_template->render('ctypes_relations', [
            'ctype' => $ctype,
            'grid'  => $grid
        ]);
    }

}

<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesProps extends cmsAction {

    public function run($ctype_id = null, $category_id = false) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->dispatchEvent('ctype_loaded', [$ctype, 'props']);

        $cats = $this->model_backend_content->getSubCategories($ctype['name']);

        $props = $this->model_backend_content->orderBy('title')->getContentProps($ctype['name']);

        $grid = $this->loadDataGrid('ctype_props', $this->cms_template->href_to('ctypes', ['props_reorder', $ctype['name']]));

        if ($this->request->isAjax()) {

            $this->model_backend_content->orderBy('ordering', 'asc');

            $fields = $this->model_backend_content->getContentPropsBinds($ctype['name'], $category_id);

            $this->cms_template->renderGridRowsJSON($grid, $fields);

            return $this->halt();
        }

        return $this->cms_template->render('ctypes_props', [
            'ctype' => $ctype,
            'cats'  => $cats,
            'props' => $props,
            'grid'  => $grid
        ]);
    }

}

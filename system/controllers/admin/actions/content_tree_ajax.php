<?php

class actionAdminContentTreeAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $id = $this->request->get('id', '');

        if (!$id || !preg_match('/^([0-9\.]+)$/i', $id)) {
            cmsCore::error404();
        }

        list ($ctype_id, $parent_id) = explode('.', $id);

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $items = $this->model_backend_content->getSubCategoriesTree($ctype['name'], $parent_id);

        $tree_nodes = [];

        if ($items) {
            foreach ($items as $item) {
                $tree_nodes[] = [
                    'title'    => $item['title'],
                    'key'      => "{$ctype_id}.{$item['id']}",
                    'isLazy'   => ($item['ns_right'] - $item['ns_left'] > 1),
                    'isFolder' => true
                ];
            }
        }

        $this->cms_template->renderJSON($tree_nodes);
    }

}

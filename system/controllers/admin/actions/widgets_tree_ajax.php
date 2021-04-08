<?php

class actionAdminWidgetsTreeAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $controller_name = $this->request->get('controller_name', '');
        if (!$controller_name) { cmsCore::error404(); }

        cmsCore::loadControllerLanguage($controller_name);

        $pages = $this->model_backend_widgets->getControllerPages($controller_name);

        $tree_nodes = [];

        if ($pages) {
            foreach ($pages as $page) {
                $tree_nodes[] = [
                    'title'  => $page['title'],
                    'key'    => "{$page['controller']}.{$page['id']}",
                    'isLazy' => false
                ];
            }
        }

        return $this->cms_template->renderJSON($tree_nodes);
    }

}

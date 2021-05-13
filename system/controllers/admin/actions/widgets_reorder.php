<?php

class actionAdminWidgetsReorder extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $items = $this->request->get('items', []);
        if (!$items) {
            return cmsCore::error404();
        }

        $position = $this->request->get('position', '');
        $page_id  = $this->request->get('page_id', 0);
        $template = $this->request->get('template', '');

        $new = $this->model_backend_widgets->reorderWidgetsBindings($position, $items, $template, $page_id);

        return $this->cms_template->renderJSON([
            'error' => false,
            'new'   => $new
        ]);
    }

}

<?php

class actionAdminWidgetsToggle extends cmsAction {

    public function run($id = null) {

        if (!$id || !is_numeric($id)) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $i = $this->model->getItemByField('widgets_bind_pages', 'id', $id);

        $is_active = $i['is_enabled'] ? 0 : 1;

        $this->model_backend_widgets->updateWidgetBindPage($id, ['is_enabled' => $is_active]);

        return $this->cms_template->renderJSON([
            'error' => false,
            'is_on' => (bool) $is_active
        ]);
    }

}

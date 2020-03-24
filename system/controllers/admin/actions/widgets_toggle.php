<?php

class actionAdminWidgetsToggle extends cmsAction {

    public function run($id = null) {

        if (!$id || !is_numeric($id)) {
            return $this->cms_template->renderJSON(array(
                'error' => true,
            ));
        }

        $i = $this->model->getItemByField('widgets_bind_pages', 'id', $id);

        $is_active = $i['is_enabled'] ? 0 : 1;

        $this->model->update('widgets_bind_pages', $id, array('is_enabled' => $is_active));

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'is_on' => (bool) $is_active
        ));

    }

}

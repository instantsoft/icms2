<?php

class actionAdminWidgetsDelete extends cmsAction {

    public function run($bp_id = false) {

        if (!$this->request->isAjax() || !$bp_id) {
            return cmsCore::error404();
        }

        $widgets_model = cmsCore::getModel('widgets');

        $del_all = false;

        $bp = $widgets_model->getWidgetBindPage($bp_id);

        if ($bp) {

            $count = $widgets_model->getWidgetBindPageCount($bp['bind_id']);

            if ($count < 2) {

                $widgets_model->deleteWidgetBinding($bp['bind_id']);

                $del_all = $bp['bind_id'];

            } else {

                $widgets_model->deleteWidgetPageBind($bp_id);

            }

        }

        return $this->cms_template->renderJSON(array(
            'errors' => false,
            'del_id' => $del_all
        ));

    }

}

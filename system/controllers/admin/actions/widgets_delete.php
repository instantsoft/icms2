<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgetsDelete extends cmsAction {

    public function run($bp_id = false) {

        if (!$this->request->isAjax() || !$bp_id) {
            return cmsCore::error404();
        }

        $del_all = false;

        $bp = $this->model_backend_widgets->getWidgetBindPage($bp_id);

        if ($bp) {

            $count = $this->model_backend_widgets->getWidgetBindPageCount($bp['bind_id']);

            if ($count < 2) {

                $this->model_backend_widgets->deleteWidgetBinding($bp['bind_id']);

                $del_all = $bp['bind_id'];
            } else {

                $this->model_backend_widgets->deleteWidgetPageBind($bp_id);
            }

            list($bp, $this->model_backend_widgets, $del_all) = cmsEventsManager::hook('widget_after_delete', [$bp, $this->model_backend_widgets, $del_all]);
        }

        return $this->cms_template->renderJSON([
            'errors' => false,
            'del_id' => $del_all
        ]);
    }

}

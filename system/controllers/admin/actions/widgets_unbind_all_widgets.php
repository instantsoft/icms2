<?php

class actionAdminWidgetsUnbindAllWidgets extends cmsAction {

    public function run($template_name = null) {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        if ($template_name) {
            $this->model_backend_widgets->unbindAllWidgets($template_name);
        }

        return $this->redirectBack();
    }

}

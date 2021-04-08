<?php

class actionAdminWidgetsUnbindAllWidgets extends cmsAction {

    public function run($template_name = null) {

        if ($template_name) {
            $this->model_backend_widgets->unbindAllWidgets($template_name);
        }

        $this->redirectBack();
    }

}

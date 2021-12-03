<?php

class actionFormsFramejs extends cmsAction {

    public function run($hash) {

        if (!$this->isAllowEmbed() || is_numeric($hash)) {
            cmsCore::error404();
        }

        $form_data = $this->model->getForm($hash);
        if (!$form_data) {
            cmsCore::error404();
        }

        header('Content-type: text/javascript');

        return $this->cms_template->renderPlain([
            'form_data' => $form_data
        ]);
    }

}

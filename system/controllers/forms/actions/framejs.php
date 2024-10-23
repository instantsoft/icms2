<?php

class actionFormsFramejs extends cmsAction {

    public function run($hash) {

        if (!$this->isAllowEmbed() || is_numeric($hash)) {
            return cmsCore::error404();
        }

        $form_data = $this->model->getForm($hash);
        if (!$form_data) {
            return cmsCore::error404();
        }

        $this->cms_core->response->setHeader('Content-Type', 'text/javascript');

        return $this->cms_template->renderPlain([
             'form_data' => $form_data
        ]);
    }

}

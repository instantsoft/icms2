<?php

class actionSearchOpensearch extends cmsAction {

    public function run() {

        $this->cms_core->response->setHeader('Content-Type', 'text/xml; charset=utf-8');

        return $this->cms_template->renderPlain('opensearch', [
            'site_config' => $this->cms_config
        ]);
    }

}

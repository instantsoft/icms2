<?php

class actionAdminLoadIcmsSponsorship extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $items = $this->getAddonsMethod('get.sponsorships');

        return $this->cms_template->renderJSON($items['response']['items'] ?? []);
    }

}

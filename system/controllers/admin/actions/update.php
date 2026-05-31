<?php

class actionAdminUpdate extends cmsAction {

    public function run() {

        $updater = new cmsUpdater();

        return $this->cms_template->render('update', [
            'update'          => $updater->checkUpdate(),
            'current_version' => cmsCore::getVersionArray()
        ]);
    }

}

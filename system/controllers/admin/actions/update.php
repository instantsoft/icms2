<?php

class actionAdminUpdate extends cmsAction {

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do) {
            return $this->runExternalActionIfExists('update_' . $do, array_slice($this->params, 1));
        }

        $updater = new cmsUpdater();

        return $this->cms_template->render('update', [
            'update'          => $updater->checkUpdate(),
            'current_version' => cmsCore::getVersionArray()
        ]);
    }

}

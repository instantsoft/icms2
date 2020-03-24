<?php

class actionAdminUpdate extends cmsAction {

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('update_'.$do, array_slice($this->params, 1));
            return;
        }

        $updater = new cmsUpdater();

        return $this->cms_template->render('update', array(
            'update'          => $updater->checkUpdate(),
            'current_version' => cmsCore::getVersionArray()
        ));

    }

}

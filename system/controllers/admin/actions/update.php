<?php

class actionAdminUpdate extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('update_'.$do, array_slice($this->params, 1));
            return;
        }

        $updater = new cmsUpdater();

        return cmsTemplate::getInstance()->render('update', array(
            'updater' => $updater,
        ));

    }

}

<?php

class actionAdminSettingsScheduler extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('settings_scheduler_'.$do, array_slice($this->params, 1));
            return;
        }

        $grid = $this->loadDataGrid('scheduler', false, 'admin.grid_filter.set_scheduler');

        return cmsTemplate::getInstance()->render('settings_scheduler', array(
            'grid' => $grid
        ));

    }

}

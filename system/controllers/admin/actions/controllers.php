<?php

class actionAdminControllers extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('controllers_'.$do, array_slice($this->params, 1));
            return;
        }

        $grid = $this->loadDataGrid('controllers', false, 'admin.grid_filter.controllers');

        return cmsTemplate::getInstance()->render('controllers', array(
            'grid' => $grid
        ));

    }

}

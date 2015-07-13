<?php

class actionAdminCtypes extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('ctypes_'.$do, array_slice($this->params, 1));
            return;
        }

        $grid = $this->loadDataGrid('ctypes');

        return cmsTemplate::getInstance()->render('ctypes', array(
            'grid' => $grid
        ));

    }

}

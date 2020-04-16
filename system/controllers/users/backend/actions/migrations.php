<?php

class actionUsersMigrations extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('migrations');

        return cmsTemplate::getInstance()->render('backend/migrations', array(
            'grid' => $grid
        ));

    }

}

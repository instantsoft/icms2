<?php

class actionComplaintsLog extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('log');

            return cmsTemplate::getInstance()->render('backend/log', array(
                    
                'grid' => $grid
        ));
    }
}
<?php

class actionUsersTabs extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('tabs');

        return cmsTemplate::getInstance()->render('backend/tabs', array(
            'grid' => $grid
        ));

    }

}

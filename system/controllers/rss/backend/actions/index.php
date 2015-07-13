<?php

class actionRssIndex extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('feeds');

        return cmsTemplate::getInstance()->render('backend/index', array(
            'grid' => $grid
        ));

    }

}

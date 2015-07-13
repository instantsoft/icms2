<?php

class actionUsersFields extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('fields');

        return cmsTemplate::getInstance()->render('backend/fields', array(
            'grid' => $grid
        ));

    }

}

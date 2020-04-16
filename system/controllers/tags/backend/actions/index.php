<?php

class actionTagsIndex extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('tags');

        return cmsTemplate::getInstance()->render('backend/tags', array(
            'grid' => $grid
        ));

    }

}

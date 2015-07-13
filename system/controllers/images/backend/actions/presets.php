<?php

class actionImagesPresets extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('presets');

        return cmsTemplate::getInstance()->render('backend/presets', array(
            'grid' => $grid
        ));

    }

}



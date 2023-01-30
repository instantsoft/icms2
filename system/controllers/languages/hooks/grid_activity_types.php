<?php

class onLanguagesGridActivityTypes extends cmsAction {

    public function run($grid){

        if(empty($this->options['sources']['activity']['types'])){
            return $grid;
        }

        $this->model->addLanguagesFields([
            'activity_types' => ['description']
        ]);

        return $grid;
    }

}

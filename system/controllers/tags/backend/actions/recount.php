<?php

class actionTagsRecount extends cmsAction {

    public function run(){

        $this->model->recountTagsFrequency();

        $this->redirectBack();

    }

}
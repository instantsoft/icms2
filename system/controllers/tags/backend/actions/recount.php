<?php

class actionTagsRecount extends cmsAction {

    public function run(){

        $tags_model = cmsCore::getModel('tags');

        $tags_model->recountTagsFrequency();

        $this->redirectBack();

    }

}

<?php

class actionTagsDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $tags_model = cmsCore::getModel('tags');

        $tags_model->deleteTag($id);

        $this->redirectBack();

    }

}

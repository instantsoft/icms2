<?php

class actionTagsDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $this->model->deleteTag($id);

        $this->redirectBack();

    }

}

<?php

class actionContentFolderDelete extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        $folder = $this->model->getContentFolder($id);

        if (!$folder) { cmsCore::error404(); }

        if (($folder['user_id'] != $user->id) && !$user->is_admin){
            cmsCore::error404();
        }

        $ctype = $this->model->getContentType($folder['ctype_id']);

        $this->model->deleteContentFolder($folder);

        $this->redirect( href_to('users', $folder['user_id'], array('content', $ctype['name'])) );

    }

}

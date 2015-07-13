<?php

class actionPhotosRename extends cmsAction{

    public function run(){

		if (!$this->request->isAjax()) { cmsCore::error404(); }
		
		$photo_id = $this->request->get('id');
		$new_title = $this->request->get('title');
		
        if (!$photo_id || !$new_title) { cmsCore::error404(); }

        $photo = $this->model->getPhoto($photo_id);

        $success = true;

        // проверяем наличие доступа
        $user = cmsUser::getInstance();
        if (!cmsUser::isAllowed('albums', 'edit')) { $success = false; }
        if (!cmsUser::isAllowed('albums', 'edit', 'all') && $photo['user_id'] != $user->id) { $success = false; }

        if (!$success){
            cmsTemplate::getInstance()->renderJSON(array(
                'success' => false
            ));
        }

        $this->model->renamePhoto($photo_id, $new_title);

        cmsTemplate::getInstance()->renderJSON(array(
            'success' => true
        ));

    }

}

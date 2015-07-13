<?php

class actionPhotosDelete extends cmsAction{

    public function run($photo_id = null){

		if (!$this->request->isAjax()) { cmsCore::error404(); }
		
        if (!$photo_id) { 
			$photo_id = $this->request->get('id');
			if (!$photo_id) { cmsCore::error404(); }
		}

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
		
		$album = cmsCore::getModel('content')->getContentItem('albums', $photo['album_id']);		

        $this->model->deletePhoto($photo_id);

        $this->model->setRandomAlbumCoverImage($photo['album_id']);

        cmsTemplate::getInstance()->renderJSON(array(
            'success' => true,
			'album_url' => href_to('albums', $album['slug'].'.html')
        ));

    }

}

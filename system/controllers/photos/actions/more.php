<?php

class actionPhotosMore extends cmsAction{

    public function run($target = null, $id = null){

		if (!$this->request->isAjax()) { cmsCore::error404(); }

        if($target === 'album_id'){

            $album = $this->model->getAlbum($id);
            if (!$album){ $this->halt(); }

            $ctype = $album['ctype'];

            list($ctype, $album, $fields) = cmsEventsManager::hook('content_albums_before_item', array($ctype, $album, array()));

            return $this->runHook('content_albums_item_html', array($album));

        } elseif($target === 'user_id'){

            $profile = cmsCore::getModel('users')->getUser($id);
            if (!$profile) { $this->halt(); }

            $profile['user_id'] = $profile['id']; // проверка на авторство идёт по полю user_id

            $ctype = cmsCore::getModel('content')->getContentTypeByName('albums');

            return $this->runHook('content_albums_items_html', array(array('user_view', $ctype, $profile, array())));

        } elseif($target === 'camera'){

            $camera = urldecode($this->request->get('camera', ''));
            if(!$camera){ cmsCore::error404(); }

            if (cmsUser::isAllowed('albums', 'view_all') || $this->cms_user->id == $profile['user_id']) {
                $this->model->disablePrivacyFilter();
            }

            $this->model->filterEqual('camera', $camera);

            $item = array(
                'id'         => 0,
                'user_id'    => 0,
                'url_params' => array('camera' => $camera),
                'base_url'   => href_to('photos', 'camera-' . urlencode($camera))
            );

            return $this->renderPhotosList($item, '', $this->request->get('photo_page', 1));

        }

        cmsCore::error404();

    }

}

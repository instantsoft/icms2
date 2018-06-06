<?php

class actionPhotosCamera extends cmsAction{

    public function run(){

        $camera = urldecode($this->request->get('name', ''));
        if(!$camera){ cmsCore::error404(); }

        if (cmsUser::isAllowed('albums', 'view_all')) {
            $this->model->disablePrivacyFilter();
        }

        $this->model->filterEqual('camera', $camera);

        $page    = $this->request->get('photo_page', 1);
        $perpage = (empty($this->options['limit']) ? 16 : $this->options['limit']);

        $this->model->limitPagePlus($page, $perpage);

        $this->model->orderBy($this->options['ordering'], $this->options['orderto']);

        $photos = $this->getPhotosList();
        if (!$photos) { cmsCore::error404(); }

        if($photos && (count($photos) > $perpage)){
            $has_next = true; array_pop($photos);
        } else {
            $has_next = false;
        }

        $ctype = cmsCore::getModel('content')->getContentTypeByName('albums');

        $this->cms_template->render('camera', array(
            'page_title'   => sprintf(LANG_PHOTOS_CAMERA_TITLE, $camera),
            'ctype'        => $ctype,
            'page'         => $page,
            'row_height'   => $this->getRowHeight(),
            'user'         => $this->cms_user,
            'item'         => array(
                'id'         => 0,
                'user_id'    => 0,
                'url_params' => array('camera' => $camera),
                'base_url'   => href_to('photos', 'camera-' . urlencode($camera))
            ),
            'item_type' => 'camera',
            'photos'       => $photos,
            'is_owner'     => cmsUser::isAllowed('albums', 'delete', 'all'),
            'has_next'     => $has_next,
            'hooks_html'   => cmsEventsManager::hookAll('photo_camera_html', $camera),
            'preset_small' => $this->options['preset_small']
        ));

    }

}

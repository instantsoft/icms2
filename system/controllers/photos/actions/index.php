<?php
class actionPhotosIndex extends cmsAction {

    public function run(){

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentTypeByName('albums');

        $album = array(
            'id'         => 0,
            'user_id'    => -1,
            'title'      => '',
            'seo_desc'   => '',
            'is_public'  => 0,
            'url_params' => array('photo_page' => 1)
        );

        list($ctype, $album, $fields) = cmsEventsManager::hook('content_albums_before_item', array($ctype, $album, array()));

        $this->model->orderByList(array(
            array(
                'by' => $album['filter_values']['ordering'],
                'to' => $album['filter_values']['orderto']
            ),
            array(
                'by' => 'id',
                'to' => $album['filter_values']['orderto']
            )
        ));

        if (cmsUser::isAllowed('albums', 'view_all') || $this->cms_user->id == $album['user_id']) {
            $this->model->disablePrivacyFilter();
        }

        if($album['filter_values']['type']){
            $this->model->filterEqual('type', $album['filter_values']['type']);
        }

        if($album['filter_values']['orientation']){
            $this->model->filterEqual('orientation', $album['filter_values']['orientation']);
        }

        if($album['filter_values']['width']){
            $this->model->filterGtEqual('width', $album['filter_values']['width']);
        }

        if($album['filter_values']['height']){
            $this->model->filterGtEqual('height', $album['filter_values']['height']);
        }

        $page    = $this->request->get('photo_page', 1);
        $perpage = (empty($this->options['limit']) ? 16 : $this->options['limit']);

        return $this->cms_template->render('index', array(
            'photos_html' => $this->renderPhotosList($album, 0, $page, $perpage),
            'album' => $album
        ));

    }

}

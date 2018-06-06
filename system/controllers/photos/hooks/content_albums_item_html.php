<?php

class onPhotosContentAlbumsItemHtml extends cmsAction {

    public function run($album){

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
            $this->model->disableApprovedFilter();
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

        $page    = $this->cms_core->request->get('photo_page', 1);
        $perpage = (empty($this->options['limit']) ? 16 : $this->options['limit']);

        $toolbar_html = cmsEventsManager::hookAll('photos_toolbar_html', $album);
        if ($toolbar_html) {
            $this->cms_template->addToBlock('before_body', html_each($toolbar_html));
        }

        return $this->renderPhotosList($album, 'album_id', $page, $perpage);

    }

}

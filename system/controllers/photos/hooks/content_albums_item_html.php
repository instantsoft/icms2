<?php

class onPhotosContentAlbumsItemHtml extends cmsAction {

    public function run($album){

        $core = cmsCore::getInstance();
        $template = cmsTemplate::getInstance();
        $user = cmsUser::getInstance();

        $page = $core->request->get('page', 1);
        $perpage = 16;

        $total = $this->model->getPhotosCount($album['id']);

        $this->model->limitPage($page, $perpage);

        $photos = $this->model->getPhotos($album['id']);

        if (!$photos && $page > 1) { $this->redirect(href_to('albums', $album['slug'].'.html')); }

        $is_owner =
            cmsUser::isAllowed('albums', 'delete', 'all') ||
            (cmsUser::isAllowed('albums', 'delete', 'own') && $album['user_id'] == $user->id);

        return $template->renderInternal($this, 'album', array(
            'album' => $album,
            'photos' => $photos,
            'page' => $page,
            'perpage' => $perpage,
            'total' => $total,
            'is_owner' => $is_owner,
            'page_url' => ''
        ));

    }

}

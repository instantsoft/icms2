<?php

class actionAdminContentCatsEdit extends cmsAction {

    public function run($ctype_id=false, $category_id=false){

        if (!$ctype_id) { $this->redirectBack(); }
        if (!$category_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $back_url = $this->request->get('back');

        if (!$back_url) { $back_url = href_to($this->name, 'content'); }

        $url = href_to($ctype['name'], 'editcat', $category_id) . '?back=' . $back_url;

        $this->redirect($url);

    }

}

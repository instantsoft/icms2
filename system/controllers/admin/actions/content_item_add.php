<?php

class actionAdminContentItemAdd extends cmsAction {

    public function run($ctype_id, $category_id=1){

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { return cmsCore::error404(); }

        $params = $category_id>1 ? array($category_id) : false;

        $url = href_to($ctype['name'], 'add', $params) . '?back=' . href_to($this->name, 'content');

        $this->redirect($url);

    }

}

<?php

class actionAdminContentCatsDelete extends cmsAction {

    public function run($ctype_id, $category_id=false){

        if (!$category_id) { cmsCore::error404(); }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken( $csrf_token )){
            cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $category = $content_model->getCategory($ctype['name'], $category_id);

        $back_url = $this->request->get('back', '');

        if (!$back_url) { $back_url = href_to($this->name, 'content'); }

        $url = href_to($ctype['name'], 'delcat', $category_id) . '?back=' . $back_url . '&csrf_token='.$csrf_token;

        $tree_path = $category['parent_id'] == 1 ? "{$ctype_id}.1" : "/{$ctype_id}.1/{$ctype_id}.{$category['parent_id']}";

        cmsUser::setCookiePublic('content_tree_path', $tree_path);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirect($url);

    }

}

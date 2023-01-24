<?php
/**
 * @property \modelContent $model_backend_content
 */
class actionAdminContentCatsDelete extends cmsAction {

    public function run($ctype_id, $category_id = false) {

        if (!$category_id) {
            return cmsCore::error404();
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $category = $this->model_backend_content->getCategory($ctype['name'], $category_id);
        if (!$category) {
            return cmsCore::error404();
        }

        $back_url = $this->getRequestBackUrl(href_to($this->name, 'content'));

        $tree_path = $category['parent_id'] == 1 ? "{$ctype_id}.1" : "/{$ctype_id}.1/{$ctype_id}.{$category['parent_id']}";

        cmsUser::setCookiePublic('content_tree_path', $tree_path);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectTo($ctype['name'], 'delcat', $category_id, ['back' => $back_url, 'csrf_token' => $csrf_token]);
    }

}

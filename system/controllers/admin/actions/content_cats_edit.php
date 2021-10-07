<?php

class actionAdminContentCatsEdit extends cmsAction {

    public function run($ctype_id = false, $category_id = false) {

        if (!$ctype_id) {
            return $this->redirectBack();
        }
        if (!$category_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $back_url = $this->getRequestBackUrl(href_to($this->name, 'content'));

        $this->redirectTo($ctype['name'], 'editcat', $category_id, ['back' => $back_url]);
    }

}

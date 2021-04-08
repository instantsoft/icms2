<?php

class actionAdminCtypesPropsBind extends cmsAction {

    public function run($ctype_id, $category_id) {

        $prop_id   = $this->request->get('prop_id', 0);
        $is_childs = $this->request->get('is_childs', 0);

        if (!$prop_id) {
            $this->redirectBack();
        }

        if (!$ctype_id || !$category_id) {
            cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $cats = [$category_id];

        if ($is_childs) {
            $subcats = $this->model_backend_content->getSubCategoriesTree($ctype['name'], $category_id, false);
            if (is_array($subcats)) {
                foreach ($subcats as $cat) {
                    $cats[] = $cat['id'];
                }
            }
        }

        $this->model_backend_content->bindContentProp($ctype['name'], $prop_id, $cats);

        cmsUser::addSessionMessage(LANG_CP_PROPS_BIND_SC, 'success');

        $this->redirectToAction('ctypes', ['props', $ctype_id]);
    }

}

<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesPropsBind extends cmsAction {

    public function run($ctype_id = null, $category_id = null) {

        if (!$ctype_id || !$category_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $prop_id   = $this->request->get('prop_id', 0);
        $is_childs = $this->request->get('is_childs', 0);

        if (!$prop_id) {
            return $this->redirectToAction('ctypes', ['props', $ctype_id]);
        }

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

        return $this->cms_template->renderJSON([
            'errors'       => false,
            'success_text' => LANG_CP_PROPS_BIND_SC,
            'callback'     => 'icms.adminProps.propBinded'
        ]);
    }

}

<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesPropsToggle extends cmsAction {

    public function run($ctype_id = null, $prop_id = null) {

        if (!$ctype_id || !$prop_id) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $prop = $this->model_backend_content->getContentProp($ctype['name'], $prop_id);
        if (!$prop) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $is_in_filter = $prop['is_in_filter'] ? 0 : 1;

        $this->model_backend_content->toggleContentPropFilter($ctype['name'], $prop_id, $is_in_filter);

        return $this->cms_template->renderJSON([
            'error' => false,
            'is_on' => $is_in_filter
        ]);
    }

}

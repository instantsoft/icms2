<?php

class actionAdminCtypesRelationsDelete extends cmsAction {

    public function run($relation_id) {

        if (!$relation_id) {  cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            cmsCore::error404();
        }

        $relation = $this->model_backend_content->getContentRelation($relation_id);

        $ctype = $this->model_backend_content->getContenttype($relation['ctype_id']);

        $this->model_backend_content->deleteContentRelation($relation_id);

        $parent_field_name = "parent_{$ctype['name']}_id";

        if ($relation['target_controller'] != 'content') {

            $this->model_backend_content->setTablePrefix('');

            $target_ctype = [
                'name' => $relation['target_controller']
            ];

        } else {
            $target_ctype = $this->model_backend_content->getContentType($relation['child_ctype_id']);
        }

        if ($this->model_backend_content->isContentFieldExists($target_ctype['name'], $parent_field_name)) {

            $this->model_backend_content->deleteContentField($target_ctype['name'], $parent_field_name, 'name', true);
        }

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();
    }

}

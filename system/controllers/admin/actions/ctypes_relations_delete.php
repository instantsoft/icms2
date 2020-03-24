<?php

class actionAdminCtypesRelationsDelete extends cmsAction {

    public function run($relation_id){

        if (!$relation_id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $relation = $content_model->getContentRelation($relation_id);

        $ctype = $content_model->getContenttype($relation['ctype_id']);

        $content_model->deleteContentRelation($relation_id);

        $parent_field_name = "parent_{$ctype['name']}_id";

        if($relation['target_controller'] != 'content'){

            $content_model->setTablePrefix('');

            $target_ctype = array(
                'name' => $relation['target_controller']
            );

        } else {

            $target_ctype = $content_model->getContentType($relation['child_ctype_id']);

        }

        if ($content_model->isContentFieldExists($target_ctype['name'], $parent_field_name)){

            $content_model->deleteContentField($target_ctype['name'], $parent_field_name, 'name', true);

        }

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();

    }

}

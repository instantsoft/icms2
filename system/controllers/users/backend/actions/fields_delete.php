<?php

class actionUsersFieldsDelete extends cmsAction {

    public function run($field_id){

        if (!$field_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $content_model->setTablePrefix('');

        $content_model->deleteContentField('{users}', $field_id);

        $this->redirectToAction('fields');

    }

}
